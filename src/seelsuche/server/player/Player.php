<?php

namespace seelsuche\server\player;

use Ratchet\ConnectionInterface;
use seelsuche\server\entity\types\LivingEntity;
use seelsuche\server\Logger;
use seelsuche\server\network\protocol\outbound\ChatDispatchPacket;
use seelsuche\server\network\protocol\OutboundPacket;
use seelsuche\server\player\coop\CoopSession;

/**
 * This is NOT an abstract or final class because this player class can be extended by plugins.
 */
class Player extends LivingEntity
{
    # Storage Variables
    protected string $userId = "0000000000";
    protected array $playerData = [];

    protected bool $inCoop = false;
    protected ?CoopSession $coopSession;

    # Constants
    private string $ipAddress, $displayName = "default";
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
            try {
                PlayerManager::cachePlayer($userId, $this);
            } catch (\Exception $exception) { Logger::warning("Unable to cache player: {$exception->getMessage()}"); }
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

    /**
     * @return string The **display** name of the user. This is the name that is shown to others.
     */
    public function getDisplayName(): string{
        return $this->displayName;
    }

    /**
     * @return string The **EMAIL ADDRESS** of the user. This should not be shown to others.
     */
    public function getUsername(): string{
        return $this->playerData["information"]["username"];
    }

    public function exportStatistics(): string{
        return json_encode($this->playerData["statistics"]);
    }

    public function exportInventory(): string{
        return json_encode($this->playerData["inventory"]);
    }

    public function importData(string $serializedData): void{
        $this->playerData = json_decode($serializedData, true);

        # Define section blocks.
        $info = $this->playerData["information"];

        # Set constants.
        #TODO: Implement a display name check and prompt to create a display name.
        $this->displayName = $info["displayName"];
    }

    public function sendMessage($message, $sender): void{
        if($sender instanceof Player)
            $sender = $sender->getDisplayName();
//        if($message instanceof TextContainer)
//            $message = $message->formatMessage();

        $pk = new ChatDispatchPacket();
        $pk->message = $message;
        $pk->username = $sender;

        $this->sendDataPacket($pk);
    }

    public function close(): void{

    }

    /*
     * Co-Op Methods
     */

    public function setCoopStatus(bool $inCoop, CoopSession $session = null): void{
        $this->inCoop = $inCoop;
        if($inCoop && !is_null($session))
            $this->coopSession = $session;
        else if ($this->coopSession != null)
            $this->coopSession = null;
    }

    public function getCoopStatus(): bool{
        return $this->inCoop;
    }

    public function getCoopSession(): ?CoopSession{
        return $this->coopSession;
    }
}