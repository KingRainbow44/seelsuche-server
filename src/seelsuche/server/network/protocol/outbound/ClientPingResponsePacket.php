<?php

namespace seelsuche\server\network\protocol\outbound;

use seelsuche\server\network\protocol\OutboundPacket;

final class ClientPingResponsePacket extends OutboundPacket
{
    protected function pid(): string
    {
        return self::CLIENT_PING;
    }

    function encode(): string
    {
        $this->writeString(time());
        return $this->prepare();
    }
}