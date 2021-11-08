<?php

namespace seelsuche\server\player;

use Ratchet\ConnectionInterface;
use seelsuche\server\entity\EntityManager;
use seelsuche\server\Logger;
use seelsuche\server\network\protocol\OutboundPacket;

/**
 * This is NOT an abstract or final class because this player class can be extended by plugins.
 */
class Player
{
    # Internal Variables
    protected int $entityId = 0;

    # Storage Variables
    protected string $userId = "0000000000";

    # Constants
    private string $ipAddress;
    // This is used to identify the client when communicating over websocket.
    private int $resourceId;

    protected ConnectionInterface $connection;

    public function __construct(int $resourceId, string $ipAddress, ConnectionInterface $connection) {
        $this->connection = $connection; $this->entityId = EntityManager::getNextId();

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
        if($userId == "0000000000") # Ensures we only set a null user ID.
            $this->userId = $userId;
    }

    public function getIPAddress(): string{
        return $this->ipAddress;
    }

    public function getResourceId(): int{
        return $this->resourceId;
    }

    public function close(): void{

    }
}