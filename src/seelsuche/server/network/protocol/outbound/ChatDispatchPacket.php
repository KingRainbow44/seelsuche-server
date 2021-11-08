<?php

namespace seelsuche\server\network\protocol\outbound;

use seelsuche\server\network\protocol\OutboundPacket;

final class ChatDispatchPacket extends OutboundPacket
{
    /** @var string */
    public string $message = "";
    /**
     * @var bool TLDR: True = Read user ID. False = Push to team chat.
     * Read the documentation.
     */
    public bool $location = false;
    /**
     * @var string
     * @optional
     */
    public string $username = "";

    protected function pid(): string
    {
        return self::CHAT_DISPATCH_PACKET;
    }

    public function encode(): string
    {
        $this->writeString($this->message);
        $this->writeBool($this->location);
        if($this->location)
            $this->writeString($this->username);
        return $this->prepare();
    }
}