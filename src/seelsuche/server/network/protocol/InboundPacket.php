<?php

namespace seelsuche\server\network\protocol;

use seelsuche\server\network\InboundPacketIds;
use seelsuche\server\player\Player;

abstract class InboundPacket extends Packet implements InboundPacketIds
{
    public abstract function decode();

    public function handle(Player $player) { }
}