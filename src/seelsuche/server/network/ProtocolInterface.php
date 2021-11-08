<?php

namespace seelsuche\server\network;

use seelsuche\server\network\protocol\inbound\AuthenticationRequestPacket;
use seelsuche\server\network\protocol\inbound\ChatReceivePacket;
use seelsuche\server\network\protocol\inbound\ClientPingRequestPacket;
use seelsuche\server\player\Player;

interface ProtocolInterface
{
    public function handlePingResponse(Player $client, ClientPingRequestPacket $packet): void;
    public function handleClientAuthentication(Player $client, AuthenticationRequestPacket $packet): void;
    public function handleChatPacket(Player $client, ChatReceivePacket $packet): void;
}