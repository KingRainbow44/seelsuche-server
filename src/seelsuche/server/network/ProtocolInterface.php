<?php

namespace seelsuche\server\network;

use seelsuche\server\network\protocol\inbound\AuthenticationRequestPacket;
use seelsuche\server\network\protocol\inbound\ChatReceivePacket;
use seelsuche\server\network\protocol\inbound\ClientPingRequestPacket;
use seelsuche\server\network\protocol\inbound\CoopActionPacket;
use seelsuche\server\network\protocol\inbound\IncomingAudioPacket;
use seelsuche\server\network\protocol\inbound\PlayerPositionPacket;
use seelsuche\server\player\Player;

interface ProtocolInterface
{
    public function handlePingResponse(Player $client, ClientPingRequestPacket $packet): void;
    public function handleClientAuthentication(Player $client, AuthenticationRequestPacket $packet): void;
    public function handleChatPacket(Player $client, ChatReceivePacket $packet): void;
    public function handleAudioPacket(Player $client, IncomingAudioPacket $packet): void;
    public function handlePlayerPosition(Player $client, PlayerPositionPacket $packet): void;
    public function handleCoopAction(Player $client, CoopActionPacket $packet): void;
}