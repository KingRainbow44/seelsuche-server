<?php

namespace seelsuche\server\network\protocol\outbound;

use seelsuche\server\network\protocol\OutboundPacket;

final class AuthenticationResponsePacket extends OutboundPacket
{
    /**
     * @var int The status code to forward to the client.
     * Status codes can be found in the documentation for `ClientAuthenticationPacket`.
     */
    public int $statusCode = 200;
    /** @var string */
    public string $username = "";
    /** @var string */
    public string $inventory = "";
    /** @var string */
    public string $statistics = "";

    protected function pid(): string
    {
        return self::CLIENT_AUTH_RESPONSE;
    }

    public function encode(): string
    {
        $this->writeInt($this->statusCode);
        $this->writeString($this->username);
        $this->writeString($this->inventory);
        $this->writeString($this->statistics);
        return $this->prepare();
    }
}