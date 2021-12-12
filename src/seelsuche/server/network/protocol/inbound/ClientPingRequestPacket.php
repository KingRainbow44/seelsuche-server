<?php

namespace seelsuche\server\network\protocol\inbound;

use seelsuche\server\Logger;
use seelsuche\server\network\protocol\InboundPacket;
use seelsuche\server\player\Player;
use seelsuche\server\Server;

final class ClientPingRequestPacket extends InboundPacket
{
    /** @var string */
    public string $clientTime;

    protected function pid(): string
    {
        return self::CLIENT_PING_RESPONSE;
    }

    function decode()
    {
        $this->clientTime = (string) $this->next();
    }

    function handle(Player $player)
    {
        if (Server::getInstance()->getConfig()->isDebugEnabled())
            Logger::debug("Time response packet received. Time from client: $this->clientTime");
        Server::getProtocolInterface()->handlePingResponse($player, $this);
    }
}