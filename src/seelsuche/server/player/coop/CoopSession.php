<?php

namespace seelsuche\server\player\coop;

use JetBrains\PhpStorm\Pure;

use seelsuche\server\network\protocol\outbound\CoopStatusPacket;
use seelsuche\server\player\Player;
use seelsuche\server\player\PlayerManager;
use seelsuche\server\world\WorldType;

final class CoopSession
{
    private Player $host;

    /** @var Player[] */
    private array $playingWith = [];
    /** @var Player[] */
    private array $invited = [];
    /** @var Player[] */
    private array $joinRequests = [];

    public function __construct(Player $host)
    {
        $this->host = $host;

        # Change host status.
        $host->setCoopStatus(true, $this);
        $host->setWorld(WorldType::COOP_GAME_WORLD);
    }

    public function disband(): void{
        foreach($this->playingWith as $player) {
            $this->removePlayer($player);
        }

        $this->host->setCoopStatus(false);
        $this->host->setWorld(WorldType::GAME_WORLD);
    }

    /**
     * Invites a player to join the co-op session.
     * The player variable can be a user id, or a player instance.
     */
    public function invitePlayer($plr): void{
        if(($player = PlayerManager::dynamicallyGetPlayer($plr)) == null) return;

        $this->invited[$player->getUserId()] = $player;

        if(isset($this->joinRequests[$player->getUserId()])) {
            # The player has been accepted to join the world.
            $this->addPlayer($player);
        } else {
            # The host is sending an invitation to the player.
            $pk = new CoopStatusPacket();
            $pk->action = CoopStatusPacket::INVITATION;
            $pk->displayName = $this->host->getDisplayName();
            $player->sendDataPacket($pk);
        }
    }

    /**
     * Asks the host of the co-op world if the player can join.
     * @var mixed $plr The person requesting to join.
     */
    public function askToJoin(mixed $plr): void{
        if(($player = PlayerManager::dynamicallyGetPlayer($plr)) == null) return;

        $this->joinRequests[$player->getUserId()] = $player;

        $pk = new CoopStatusPacket();
        $pk->action = CoopStatusPacket::JOIN_REQUEST;
        $pk->displayName = $player->getDisplayName();
        $this->host->sendDataPacket($pk);
    }

    public function addPlayer(Player $player): void{
        if(!isset($this->invited[$player->getUserId()]))
            return;

        $this->playingWith[$player->getUserId()] = $player;
        unset($this->invited[$player->getUserId()]);
        unset($this->joinRequests[$player->getUserId()]);

        #TODO: Move the player to the co-op world.
        $player->setCoopStatus(true, $this);
        #TODO: Send join packet to host client.
    }

    public function removePlayer($plr, bool $kicked = false): void{
        if(($player = PlayerManager::dynamicallyGetPlayer($plr)) == null) return;

        if(isset($this->playingWith[$player->getUserId()])) {
            unset($this->playingWith[$player->getUserId()]);

            $player->setCoopStatus(false);

            $pk = new CoopStatusPacket();
            $pk->action = CoopStatusPacket::KICKED;
            $pk->statusCode = ($kicked ? 403 : 200);
            $player->sendDataPacket($pk);
        }
    }

    #[Pure] public function getPlayingWith(bool $includeHost = false, array $exclude = []): array{
        $players = $this->playingWith;
        if($includeHost)
            $players[$this->host->getUserId()] = $this->host;
        if(!empty($exclude))
            $players = array_diff_key($players, array_flip($exclude));
        return $players;
    }
}