# FooScore

Foosball app for your team.

This app allows you to score goals in order to rank players.

> The purpose of this app is mainly to learn how to apply DDD / CQRS / Event Sourcing with PHP.

## Business rules

The first team that scores 5 goals wins the game.

If a goal comes from the midfield, we put two points into play. The next goal will count for two points.
If there is another goal from a midfield, we put 3 points into play, etc.

Each player has an ELO rank that allows matchmaking. See https://bonziniusa.com/player-resources/rating-system/.

Players can see the ELO chart to compare to others.

## Start the server
    make start

## Launch tests
    make test

### Credits

Developed by [Lucas Courot](https://github.com/lucascourot).
