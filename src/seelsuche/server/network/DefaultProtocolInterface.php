<?php

namespace seelsuche\server\network;

use seelsuche\server\network\protocol\inbound\AuthenticationRequestPacket;
use seelsuche\server\network\protocol\inbound\ChatReceivePacket;
use seelsuche\server\network\protocol\inbound\ClientPingRequestPacket;
use seelsuche\server\network\protocol\outbound\ClientPingResponsePacket;
use seelsuche\server\network\protocol\outbound\AuthenticationResponsePacket;
use seelsuche\server\player\Player;
use seelsuche\server\Server;

/**
 * A protocol interface is what handles all the incoming packets.
 * This is where the packets get processed and where most protocol-related actions take place.
 */
class DefaultProtocolInterface implements ProtocolInterface
{
    public function handlePingResponse(Player $client, ClientPingRequestPacket $packet): void
    {
        $response = new ClientPingResponsePacket();
        $client->sendDataPacket($response);
    }

    public function handleClientAuthentication(Player $client, AuthenticationRequestPacket $packet): void
    {
        # TODO: Implement rate limiting with error code '429'
        $database = Server::getInstance()->getDatabase();
        $database->query("SELECT * FROM `users` WHERE `username`='$packet->username';", function($rows) use ($database, $client, $packet) {
            if(empty($rows)) {
                $pk = new AuthenticationResponsePacket();
                $pk->statusCode = 404;
                $client->sendDataPacket($pk);
                return;
            }

            $entry = $rows[0]; # This *should* contain the data for the player we want, since user IDs and usernames are different.
            if($entry["hash"] !== substr($packet->credentials, 0, -2)) {
                $pk = new AuthenticationResponsePacket();
                $pk->statusCode = 403;
                $client->sendDataPacket($pk);
                return;
            }

            $client->setUserId($entry["userId"]);
            $pk = new AuthenticationResponsePacket();
            $pk->username = $entry["username"];
            $client->sendDataPacket($pk);

            #TODO: Implement the user data system.
//            $pk->inventory = $entry["..."];
//            $pk->statistics = $entry["..."];
        });
    }

    public function handleChatPacket(Player $client, ChatReceivePacket $packet): void
    {
        // TODO: Implement chat.
    }
}