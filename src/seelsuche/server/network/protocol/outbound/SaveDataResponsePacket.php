<?php

namespace seelsuche\server\network\protocol\outbound;

use seelsuche\server\network\protocol\OutboundPacket;

final class SaveDataResponsePacket extends OutboundPacket
{
    public bool $dataSaved = false;
    public string $saveTime = '0';

    protected function pid(): string
    {
        return self::SAVE_DATA_RESPONSE;
    }

    public function encode(): string
    {
        $this->writeBool($this->dataSaved);
        $this->writeString($this->saveTime);
        return $this->prepare();
    }
}