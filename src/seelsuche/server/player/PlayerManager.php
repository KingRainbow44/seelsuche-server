<?php

namespace seelsuche\server\player;

use Ratchet\ConnectionInterface;

final class PlayerManager
{
    /** @var Player[] */
    private static array $players = [];
    /** @var Player[] */
    private static array $userCache = [];
    /** @var Player[] */
    private static array $displayCache = [];

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

    /**
     * @throws \Exception
     */
    public static function cachePlayer(string $userId, Player $player): void{
        if(isset(self::$userCache[$userId]))
            throw new \Exception("Unable to add another player with user ID: $userId. Player already exists.");
        self::$userCache[$userId] = $player; self::$displayCache[$player->getDisplayName()] = $player;
    }

    public static function dynamicallyGetPlayer($plr): ?Player{
        $player = null;
        if($plr instanceof Player)
            $player = $plr;
        else {
            $fromId = PlayerManager::getPlayerByUserId($plr);
            if($fromId != null) $player = $fromId;
        }

        return $player;
    }

    public static function getPlayer(int $resourceId): ?Player{
        return self::$players[$resourceId] ?? null;
    }

    public static function getPlayerByUserId(string $userId): ?Player{
        return self::$userCache[$userId] ?? null;
    }

    public static function getPlayers(): array{
        return self::$players;
    }

    public static function removePlayer(int $resourceId, int $reason = -1): void{
        #TODO: Close player connections, remove from worlds, etc...

        $player = self::$players[$resourceId];
        if($player != null) {
            $player->close();

            unset(self::$userCache[$player->getUserId()]);
        }
        unset(self::$players[$resourceId]);
    }
}