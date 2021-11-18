<?php

namespace seelsuche\server\network\protocol\inbound;

use seelsuche\server\network\protocol\InboundPacket;
use seelsuche\server\player\Player;
use seelsuche\server\Server;

final class CoopActionPacket extends InboundPacket
{
    public int $action;
    public string $userId;

    public const START_SESSION = 0;
    public const DISBAND_SESSION = 1;
    public const INVITE_PLAYER = 2;
    public const KICK_PLAYER = 3;
    public const JOIN_SESSION = 4;

    protected function pid(): string
    {
        return self::COOP_ACTION_PACKET;
    }

    public function decode()
    {
        $this->action = (int) $this->next();
        switch($this->action) {
            case self::INVITE_PLAYER:
            case self::KICK_PLAYER:
            case self::JOIN_SESSION:
                $this->userId = (string) $this->next();
                return;
        }
    }

    public function handle(Player $player)
    {
        Server::getProtocolInterface()->handleCoopAction($player, $this);
    }
}