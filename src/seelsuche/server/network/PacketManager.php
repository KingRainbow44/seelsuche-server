<?php

declare(strict_types=1);

namespace seelsuche\server\network;

use seelsuche\server\network\protocol\inbound\AuthenticationRequestPacket;
use seelsuche\server\network\protocol\inbound\ChatReceivePacket;
use seelsuche\server\network\protocol\inbound\ClientPingRequestPacket;
use seelsuche\server\network\protocol\inbound\CoopActionPacket;
use seelsuche\server\network\protocol\inbound\IncomingAudioPacket;
use seelsuche\server\network\protocol\inbound\PlayerPositionPacket;
use seelsuche\server\network\protocol\InboundPacket;
use seelsuche\server\network\protocol\Packet;
use seelsuche\server\player\Player;

final class PacketManager
{
    /** @var Packet[] */
    private static array $packets = [];

    /**
     * This should register inbound packets ONLY.
     */
    public static function initialize(): void{
        self::registerPacket("0x01", ClientPingRequestPacket::class);
        self::registerPacket("0x02", AuthenticationRequestPacket::class);
        self::registerPacket("0x03", ChatReceivePacket::class);
        self::registerPacket("0x04", IncomingAudioPacket::class);
        self::registerPacket("0x05", PlayerPositionPacket::class);
        self::registerPacket("0x06", CoopActionPacket::class);
    }

    /**
     * Registers a packet to the list, or replaces it if it already exists.
     */
    public static function registerPacket(string $packetId, string $packet): void{
        self::$packets[$packetId] = $packet;
    }

    /**
     * @param string $rawData
     * @param Player $client
     * Call when a new packet message is received.
     */
    public static function receivePacket(string $rawData, Player $client): void{
        $decoded = json_decode($rawData, true);

        if(!isset($decoded[0]) || $decoded[0] != "packet")
            return;
        $id = $decoded[2]; $content = $decoded[1];

        if(isset(self::$packets[$id])) {
            $class = self::$packets[$id];
            /** @var InboundPacket $packet */
            $packet = new $class($content);

            $packet->decode(); $packet->handle($client);
        }
    }
}