<?php

namespace seelsuche\server\network\protocol\inbound;

use seelsuche\server\network\protocol\InboundPacket;
use seelsuche\server\player\Player;
use seelsuche\server\Server;

final class PlayerPositionPacket extends InboundPacket
{
    public float $xOffset, $yOffset, $zOffset, $playerPitch;
    public float $cameraPitch = 0.0, $cameraYaw = 0.0;

    protected function pid(): string
    {
        return self::PLAYER_MOVEMENT_PACKET;
    }

    public function decode()
    {
        $this->xOffset = (float) $this->next();
        $this->yOffset = (float) $this->next();
        $this->zOffset = (float) $this->next();
        $this->playerPitch = (float) $this->next();
//        $this->cameraPitch = (float) $this->next();
//        $this->cameraYaw = (float) $this->next();
    }

    public function handle(Player $player)
    {
        Server::getProtocolInterface()->handlePlayerMovement($player, $this);
    }
}