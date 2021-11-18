<?php

namespace seelsuche\server\network\protocol\inbound;

use seelsuche\server\network\protocol\InboundPacket;
use seelsuche\server\player\Player;
use seelsuche\server\Server;

final class IncomingAudioPacket extends InboundPacket
{
    public int $channels, $samples, $frequency;
    public array $audioData, $receivers;

    protected function pid(): string
    {
        return self::INCOMING_AUDIO_PACKET;
    }

    public function decode()
    {
        $this->channels = (int) $this->next();
        $this->samples = (int) $this->next();
        $this->frequency = (int) $this->next();
        $this->audioData = json_decode((string) $this->next(), true);
        $this->receivers = json_decode((string) $this->next(), true);
    }

    public function handle(Player $player)
    {
        Server::getProtocolInterface()->handleAudioPacket($player, $this);
    }
}