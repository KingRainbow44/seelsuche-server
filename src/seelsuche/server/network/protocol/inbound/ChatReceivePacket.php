<?php

namespace seelsuche\server\network\protocol\inbound;

use seelsuche\server\network\protocol\InboundPacket;
use seelsuche\server\player\Player;
use seelsuche\server\Server;

final class ChatReceivePacket extends InboundPacket
{
    /** @var string */
    public string $message = "";
    /**
     * @var string
     * Can also be `team` to push to co-op chat.
     */
    public string $userId = "";

    protected function pid(): string
    {
        return self::CHAT_RECEIVE_PACKET;
    }

    public function decode()
    {
        $this->message = (string) $this->next();
        $this->userId = (string) $this->next();
    }

    public function handle(Player $player)
    {
        Server::getProtocolInterface()->handleChatPacket($player, $this);
    }
}