<?php

namespace seelsuche\server\player;

use Ratchet\ConnectionInterface;

final class PlayerManager
{
    /** @var Player[] */
    private static array $players = [];

    /**
     * @throws \Exception
     */
    public static function addPlayer(int $resourceId, string $ipAddress, ConnectionInterface $connection): void{
        if(isset(self::$players[$resourceId]))
            throw new \Exception("Unable to add another client with resource ID: $resourceId. Client already exists.");

        #TODO: Implement custom player classes.
        $class = Player::class;
        self::$players[$resourceId] = new $class($resourceId, $ipAddress, $connection);
    }

    public static function getPlayer(int $resourceId): ?Player{
        return self::$players[$resourceId] ?? null;
    }

    public static function removePlayer(int $resourceId): void{
        #TODO: Close player connections, remove from worlds, etc...

        self::$players[$resourceId]->close();
        unset(self::$players[$resourceId]);
    }
}