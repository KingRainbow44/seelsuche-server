<?php

namespace seelsuche\server\network\protocol\outbound;

use seelsuche\server\network\protocol\OutboundPacket;

final class OutgoingAudioPacket extends OutboundPacket
{
    public int $channels, $samples, $frequency;
    public array $audioData;
    public float $volume;

    protected function pid(): string
    {
        return self::OUTGOING_AUDIO_PACKET;
    }

    public function encode(): string
    {
        $this->writeInt($this->channels);
        $this->writeInt($this->samples);
        $this->writeInt($this->frequency);
        $this->writeString(json_encode($this->audioData));
        $this->writeFloat($this->volume);

        return $this->prepare();
    }
}