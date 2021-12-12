<?php

namespace seelsuche\server\player\coop;

use Exception;

use seelsuche\server\player\Player;
use seelsuche\server\player\PlayerManager;
use seelsuche\server\utils\Utility;

final class CoopSessionManager
{
    /** @var CoopSession[] */
    private static array $sessions = [];

    /**
     * @throws Exception
     */
    public static function startCoopSession(Player $host, array $toInvite = []): void{
        if(isset(self::$sessions[$host->getUserId()]))
            throw new Exception("{$host->getDisplayName()} already has a co-op session.");

        self::$sessions[$host->getUserId()] = new CoopSession($host);
        if(!empty($toInvite)) {
            if(!Utility::validateArray($toInvite, Player::class))
                return;
            foreach($toInvite as $player)
                self::$sessions[$host->getUserId()]->invitePlayer($player);
        }
    }

    /**
     * @throws Exception
     */
    public static function disbandCoopSession(Player $host): void{
        if(!isset(self::$sessions[$host->getUserId()]))
            throw new Exception("{$host->getDisplayName()} does not have a co-op session.");

        $session = self::$sessions[$host->getUserId()];
        $session->disband();

        unset(self::$sessions[$host->getUserId()]);
    }

    /**
     * @deprecated Use {@link PlayerManager::getPlayerByUserId()} {@link Player::getCoopSession()}
     */
    public static function getCoopSessionFromUserId(string $userId): ?CoopSession{
        if(!isset(self::$sessions[$userId]))
            return null;
        return self::$sessions[$userId];
    }
}