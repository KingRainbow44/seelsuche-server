<?php

namespace seelsuche\server\network;

use Exception;
use seelsuche\server\network\protocol\inbound\AuthenticationRequestPacket;
use seelsuche\server\network\protocol\inbound\ChatReceivePacket;
use seelsuche\server\network\protocol\inbound\ClientPingRequestPacket;
use seelsuche\server\network\protocol\inbound\CoopActionPacket;
use seelsuche\server\network\protocol\inbound\IncomingAudioPacket;
use seelsuche\server\network\protocol\inbound\PlayerPositionPacket;
use seelsuche\server\network\protocol\outbound\ChatDispatchPacket;
use seelsuche\server\network\protocol\outbound\ClientPingResponsePacket;
use seelsuche\server\network\protocol\outbound\AuthenticationResponsePacket;
use seelsuche\server\network\protocol\outbound\CoopStatusPacket;
use seelsuche\server\network\protocol\outbound\OutgoingAudioPacket;
use seelsuche\server\player\coop\CoopSessionManager;
use seelsuche\server\player\Player;
use seelsuche\server\player\PlayerManager;
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
            if($entry["hash"] !== str_replace(["\r", "\n"], "", $packet->credentials)) {
                $pk = new AuthenticationResponsePacket();
                $pk->statusCode = 403;
                $client->sendDataPacket($pk);
                return;
            }

            # Query the data table.
            $database->query("SELECT * FROM `data` WHERE `userId`='{$entry["userId"]}';", function ($rows) use ($client, $entry) {
                if(empty($rows)) {
                    $pk = new AuthenticationResponsePacket();
                    $pk->statusCode = 404;
                    $client->sendDataPacket($pk);
                    return;
                } $data = $rows[0];

                $client->importData($data["data"]);
                $client->setUserId($entry["userId"]);

                $pk = new AuthenticationResponsePacket();
                $pk->username = $entry["username"];
                $pk->statistics = json_encode($client->exportStatistics());
                $pk->inventory = json_encode($client->exportInventory());
                $client->sendDataPacket($pk);
            });
        });
    }

    public function handleChatPacket(Player $client, ChatReceivePacket $packet): void
    {
        $sendTo = PlayerManager::getPlayerByUserId($packet->userId);
        if($sendTo == null && $packet->userId != "team") return; #TODO: Send a response packet saying the user was not found.

        $pk = new ChatDispatchPacket();
        $pk->location = ($packet->userId != "team");
        $pk->message = $packet->message;
        $pk->username = $client->getDisplayName();

        if($sendTo != null)
            $sendTo->sendDataPacket($pk);
        else {
            #TODO: Get current player's team and send them the message.
        }
    }

    public function handleAudioPacket(Player $client, IncomingAudioPacket $packet): void
    {
        # send back test packet
        $pk = new OutgoingAudioPacket();
        $pk->channels = $packet->channels;
        $pk->samples = $packet->samples;
        $pk->frequency = $packet->frequency;
        $pk->audioData = $packet->audioData;
        $pk->volume = 0.8;

        foreach($packet->receivers as $clientId)
            PlayerManager::getPlayerByUserId($clientId)?->sendDataPacket($pk);
    }

    public function handlePlayerPosition(Player $client, PlayerPositionPacket $packet): void
    {
        // TODO: Implement handlePlayerMovement() method.
    }

    public function handleCoopAction(Player $client, CoopActionPacket $packet): void
    {
        $pk = new CoopStatusPacket();
        $pk->action = CoopStatusPacket::NO_RESPONSE;
        switch($packet->action) {
            case CoopActionPacket::START_SESSION:
                try {
                    CoopSessionManager::startCoopSession($client);
                    $pk->statusCode = 200;
                } catch (Exception) {
                    $pk->statusCode = 500;
                }
                break;
            case CoopActionPacket::DISBAND_SESSION:
                try {
                    CoopSessionManager::disbandCoopSession($client);
                    $pk->statusCode = 200;
                } catch (Exception) {
                    $pk->statusCode = 500;
                }
                break;
            case CoopActionPacket::INVITE_PLAYER:
                $session = $client->getCoopSession();
                $session?->invitePlayer($packet->userId);
                return;
            case CoopActionPacket::KICK_PLAYER:
                $session = $client->getCoopSession();
                $session?->removePlayer($packet->userId, true);
                return;
            case CoopActionPacket::JOIN_SESSION:
                $session = CoopSessionManager::getCoopSessionFromUserId($packet->userId);
                $session?->addPlayer($client);
                return;
        }
        $client->sendDataPacket($pk);
    }
}