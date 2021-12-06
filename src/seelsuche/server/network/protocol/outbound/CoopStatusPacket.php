<?php

namespace seelsuche\server\network\protocol\outbound;

use seelsuche\server\network\protocol\OutboundPacket;

final class CoopStatusPacket extends OutboundPacket
{
    public int $action;
    public int $statusCode = 200;
    public string $displayName = "";

    public const NO_RESPONSE = -1;
    public const KICKED = 5;
    public const INVITATION = 6;
    public const JOIN_REQUEST = 7;
    public const JOINING_WORLD = 8;

    protected function pid(): string
    {
        return self::COOP_STATUS_PACKET;
    }

    public function encode(): string
    {
        $this->writeInt($this->action);
        $this->writeInt($this->statusCode);
        if($this->action == self::JOIN_REQUEST || $this->action == self::INVITATION)
            $this->writeString($this->displayName);
        return $this->prepare();
    }
}