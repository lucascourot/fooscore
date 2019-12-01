<?php

declare(strict_types=1);

namespace Fooscore\Controller;

use Fooscore\Gaming\Match\GoalWasScored;
use Fooscore\Gaming\Match\MatchId;
use Fooscore\Gaming\Match\MiddlefieldGoalsWereValidatedByRegularGoal;
use Fooscore\Gaming\Match\Player;
use Fooscore\Gaming\Match\ScoreGoal;
use Fooscore\Gaming\Match\ScoreMiddlefieldGoal;
use Fooscore\Gaming\Match\Scorer;
use Fooscore\Gaming\Match\StartMatch;
use Fooscore\Gaming\Match\TeamBlue;
use Fooscore\Gaming\Match\TeamRed;
use Fooscore\Gaming\MatchDetails\MiddlefieldGoalNotFound;
use Fooscore\Gaming\MatchDetails\ShowMatchDetails;
use Fooscore\Gaming\MatchDetails\ShowMiddlefieldGoal;
use Fooscore\Identity\CanGetUsers;
use Fooscore\Identity\CanLogIn;
use Fooscore\Identity\Credentials;
use Fooscore\Ranking\CanUpdateEloScore;
use Fooscore\Ranking\LosingTeam;
use Fooscore\Ranking\MatchResult;
use Fooscore\Ranking\WinningTeam;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use function json_decode;
use function strval;

class ApiController extends AbstractController
{
    /**
     * Login
     *
     * @Route("/api/login", name="api_login", methods={"POST"})
     */
    public function login(Request $request, CanLogIn $logIn) : JsonResponse
    {
        $credentials = json_decode((string) $request->getContent(), true);
        $token = $logIn->logIn(
            new Credentials(
                $credentials['username'] ?? '',
                $credentials['password'] ?? ''
            )
        );

        if ($token === null) {
            throw new BadRequestHttpException('Cannot log in the user.');
        }

        return $this->json(['token' => $token]);
    }

    /**
     * Get players
     *
     * @Route("/api/players", name="api_players", methods={"GET"})
     */
    public function players(CanGetUsers $getUsers) : JsonResponse
    {
        return $this->json([
            'players' => $getUsers->getUsers(),
        ]);
    }

    /**
     * Start match
     *
     * @Route("/api/matches", name="api_start_match", methods={"POST"})
     */
    public function startMatch(Request $request, StartMatch $startMatch) : RedirectResponse
    {
        $players = json_decode((string) $request->getContent(), true)['players'];

        $match = $startMatch(
            new TeamBlue(
                new Player($players['blueBack']['id'], $players['blueBack']['name']),
                new Player($players['blueFront']['id'], $players['blueFront']['name'])
            ),
            new TeamRed(
                new Player($players['redBack']['id'], $players['redBack']['name']),
                new Player($players['redFront']['id'], $players['redFront']['name'])
            )
        );

        return $this->redirect($this->generateUrl('api_match', [
            'matchId' => $match->id()->value()->toString(),
        ]));
    }

    /**
     * Show match
     *
     * @Route("/api/matches/{matchId}", name="api_match", methods={"GET"})
     */
    public function showMatch(string $matchId, ShowMatchDetails $showMatchDetails) : JsonResponse
    {
        $matchWithDetail = $showMatchDetails($matchId);

        return $this->json($matchWithDetail);
    }

    /**
     * Score regular goal
     *
     * @Route("/api/matches/{matchId}/regular-goals", name="api_score_regular_goal", methods={"POST"})
     */
    public function scoreRegularGoal(
        Request $request,
        string $matchId,
        ScoreGoal $scoreGoal
    ) : Response {
        $content = json_decode((string) $request->getContent(), true);

        $match = $scoreGoal(
            new MatchId(Uuid::fromString($matchId)),
            Scorer::fromTeamAndPosition($content['team'], $content['position'])
        );

        foreach ($match->recordedEvents() as $recordedEvent) {
            $domainEvent = $recordedEvent->domainEvent();
            if ($domainEvent instanceof GoalWasScored) {
                return $this->redirect($this->generateUrl('api_regular_goal', [
                    'matchId' => $matchId,
                    'goalId' => $domainEvent->goal()->number(),
                ]));
            }

            if ($domainEvent instanceof MiddlefieldGoalsWereValidatedByRegularGoal) {
                return $this->redirect($this->generateUrl('api_regular_validation_goal', [
                    'matchId' => $matchId,
                    'goalId' => $domainEvent->goal()->number(),
                ]));
            }
        }

        return new Response('No goal has been scored');
    }

    /**
     * Show Regular Goal
     *
     * @Route("/api/matches/{matchId}/regular-goals/{goalId}", name="api_regular_goal", methods={"GET"})
     */
    public function showRegularGoal(string $matchId, string $goalId, ShowMatchDetails $showMatchDetails) : JsonResponse
    {
        $matchWithDetail = $showMatchDetails($matchId);

        $askedGoal = null;
        foreach ($matchWithDetail['goals'] as $scoredGoal) {
            if (strval($scoredGoal['id']) !== $goalId) {
                continue;
            }

            $askedGoal = $scoredGoal;
        }

        if ($askedGoal === null) {
            throw new NotFoundHttpException('Goal not found');
        }

        return $this->json($askedGoal);
    }

    /**
     * Score middlefield goal
     *
     * @Route("/api/matches/{matchId}/middlefield-goals", name="api_score_middlefield_goal", methods={"POST"})
     */
    public function scoreMiddlefieldGoal(
        Request $request,
        string $matchId,
        ScoreMiddlefieldGoal $scoreMiddlefieldGoal
    ) : Response {
        $content = json_decode((string) $request->getContent(), true);

        $goalWasAccumulated = $scoreMiddlefieldGoal(
            new MatchId(Uuid::fromString($matchId)),
            Scorer::fromTeamAndPosition($content['team'], $content['position'])
        );

        return $this->redirect($this->generateUrl('api_middlefield_goal', [
            'matchId' => $matchId,
            'goalId' => $goalWasAccumulated->goal()->number(),
        ]));
    }

    /**
     * Show Middlefield Goal
     *
     * @Route("/api/matches/{matchId}/middlefield-goals/{goalId}", name="api_middlefield_goal", methods={"GET"})
     */
    public function showMiddlefieldGoal(
        string $matchId,
        string $goalId,
        ShowMiddlefieldGoal $showMiddlefieldGoal
    ) : JsonResponse {
        $middlefieldGoal = $showMiddlefieldGoal($matchId, $goalId);

        return $this->json($middlefieldGoal);
    }

    /**
     * Show api_regular_middlefield_validation_goal
     *
     * @Route("/api/matches/{matchId}/regular-validation-goals/{goalId}", name="api_regular_validation_goal", methods={"GET"})
     */
    public function showRegularValidationGoal(
        string $matchId,
        string $goalId,
        ShowMatchDetails $showMatchDetails
    ) : JsonResponse {
        $matchWithDetail = $showMatchDetails($matchId);

        $askedGoal = null;
        foreach ($matchWithDetail['goals'] as $scoredGoal) {
            if (strval($scoredGoal['id']) !== $goalId) {
                continue;
            }

            $askedGoal = $scoredGoal;
        }

        if ($askedGoal === null) {
            throw new NotFoundHttpException('Goal not found');
        }

        return $this->json($askedGoal);
    }

    /**
     * Update Elo Score
     *
     * @Route("/api/scores", name="api_update_score", methods={"POST"})
     */
    public function updateEloScore(Request $request, CanUpdateEloScore $canUpdateEloScore) : JsonResponse
    {
        $content = json_decode((string) $request->getContent(), true);

        $eloScores = $canUpdateEloScore->updatePlayersScores(new MatchResult(
            new WinningTeam($content['winning']['playerA'], $content['winning']['playerB']),
            new LosingTeam($content['losing']['playerA'], $content['losing']['playerB'])
        ));

        return $this->json($eloScores->playersWithScores());
    }
}
