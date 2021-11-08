<?php

namespace seelsuche\server\network\protocol\inbound;

use seelsuche\server\network\protocol\InboundPacket;
use seelsuche\server\player\Player;
use seelsuche\server\Server;

final class AuthenticationRequestPacket extends InboundPacket
{
    /**
     * @var string The current UNIX timestamp.
     */
    public string $time;
    /** @var string */
    public string $ipAddress;
    /** @var string */
    public string $credentials;
    /** @var string */
    public string $username;

    protected function pid(): string
    {
        return self::CLIENT_AUTH;
    }

    public function decode()
    {
        $this->time = (string) $this->next();
        $this->ipAddress = (string) $this->next();
        $this->credentials = (string) $this->next();
        $this->username = (string) $this->next();
    }

    public function handle(Player $player)
    {
        Server::getProtocolInterface()->handleClientAuthentication($player, $this);
    }
}