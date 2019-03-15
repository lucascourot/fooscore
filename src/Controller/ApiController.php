<?php

declare(strict_types=1);

namespace Fooscore\Controller;

use Fooscore\Gaming\CanScoreGoal;
use Fooscore\Gaming\CanShowMatchDetails;
use Fooscore\Gaming\CanStartMatch;
use Fooscore\Gaming\Match\{GoalWasScored, MatchId, MatchRepository, Player, Scorer, TeamBlue, TeamRed};
use Fooscore\Identity\CanGetUsers;
use Fooscore\Identity\CanLogIn;
use Fooscore\Identity\Credentials;
use Fooscore\Ranking\CanUpdateScore;
use Ramsey\Uuid\Uuid;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    /**
     * @Route("/api/login", name="api_login", methods={"POST"})
     */
    public function login(Request $request, CanLogIn $logIn): JsonResponse
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

        return $this->json([
            'token' => $token,
        ]);
    }

    /**
     * @Route("/api/players", name="api_players", methods={"GET"})
     */
    public function players(CanGetUsers $getUsers): JsonResponse
    {
        return $this->json([
            'players' => $getUsers->getUsers(),
        ]);
    }

    /**
     * @Route("/api/matches", name="api_start_match", methods={"POST"})
     */
    public function startMatch(Request $request, CanStartMatch $startMatch): RedirectResponse
    {
        $players = json_decode((string) $request->getContent(), true)['players'];

        $match = $startMatch->startMatch(
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
     * @Route("/api/matches/{matchId}", name="api_match", methods={"GET"})
     */
    public function showMatch(string $matchId, CanShowMatchDetails $showMatchDetails): JsonResponse
    {
        $matchWithDetail = $showMatchDetails->showMatchDetails(new MatchId(Uuid::fromString($matchId)));

        return $this->json($matchWithDetail);
    }

    /**
     * @Route("/api/matches/{matchId}/goals", name="api_score_goal", methods={"POST"})
     */
    public function scoreGoal(Request $request, string $matchId, CanScoreGoal $scoreGoal): RedirectResponse
    {
        $content = json_decode((string) $request->getContent(), true);
        $type = $content['type'];
        $team = $content['team'];
        $position = $content['position'];

        $match = $scoreGoal->scoreGoal(
            new MatchId(Uuid::fromString($matchId)),
            Scorer::fromTeamAndPosition($team, $position)
        );

        foreach ($match->recordedEvents() as $recordedEvent) {
            $domainEvent = $recordedEvent->domainEvent();
            if ($domainEvent instanceof GoalWasScored) {
                return $this->redirect($this->generateUrl('api_goal', [
                    'matchId' => $matchId,
                    'goalId' => $domainEvent->goal()->number(),
                ]));
            }
        }

        throw new RuntimeException('No goal has been scored');
    }

    /**
     * @Route("/api/matches/{matchId}/goals/{goalId}", name="api_goal", methods={"GET"})
     */
    public function showGoal(string $matchId, string $goalId, CanShowMatchDetails $showMatchDetails): JsonResponse
    {
        $matchWithDetail = $showMatchDetails->showMatchDetails(new MatchId(Uuid::fromString($matchId)));

        $askedGoal = null;
        foreach ($matchWithDetail['goals'] as $scoredGoal) {
            if (strval($scoredGoal['id']) === $goalId) {
                $askedGoal = $scoredGoal;
            }
        }

        if ($askedGoal === null) {
            throw new NotFoundHttpException('Goal not found');
        }

        return $this->json($askedGoal);
    }

    /**
     * @Route("/api/score", name="api_update_score", methods={"POST"})
     */
    public function updateScore(Request $request, CanUpdateScore $updateScore): JsonResponse
    {
        $players = json_decode((string) $request->getContent(), true)['players'];

        $updateScore->updateScore(
            $players['winning'][0]['id'],
            $players['winning'][1]['id'],
            $players['losing'][0]['id'],
            $players['losing'][1]['id']
        );

        return $this->json(['success' => true]);
    }
}
