<?php

namespace seelsuche\server\player;

use Ratchet\ConnectionInterface;
use seelsuche\server\entity\types\LivingEntity;
use seelsuche\server\Logger;
use seelsuche\server\network\protocol\OutboundPacket;

/**
 * This is NOT an abstract or final class because this player class can be extended by plugins.
 */
class Player extends LivingEntity
{
    # Storage Variables
    protected string $userId = "0000000000";

    # Constants
    private string $ipAddress;
    // This is used to identify the client when communicating over websocket.
    private int $resourceId;

    protected ConnectionInterface $connection;

    public function __construct(int $resourceId, string $ipAddress, ConnectionInterface $connection) {
        parent::__construct();
        $this->connection = $connection;

        # Set constants.
        $this->ipAddress = $ipAddress;
        $this->resourceId = $resourceId;
    }

    /**
     * Forwards a data packet to the client.
     */
    public function sendDataPacket(OutboundPacket $packet): void{
        Logger::debug("Packet dispatched to $this->resourceId. Data: {$packet->encode()}");
        $this->connection->send($packet->encode());
    }

    public function setUserId(string $userId): void{
        if($this->userId == "0000000000") { # Ensures we only set a null user ID.
            $this->userId = $userId;
            PlayerManager::cachePlayer($userId, $this);
        }
    }

    public function getIPAddress(): string{
        return $this->ipAddress;
    }

    public function getResourceId(): int{
        return $this->resourceId;
    }

    public function getUserId(): string{
        return $this->userId;
    }

    public function close(): void{

    }
}