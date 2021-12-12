<?php

namespace seelsuche\server\network\protocol;

abstract class Packet
{
    protected array $toDecode = [], $toEncode = [];
    protected int $readNext = 0, $writeNext = 0;

    public function __construct(array $data = []) {
        $this->toDecode = $data;
    }

    protected abstract function pid(): string;

    /**
     * Reads the next value sent in the packet.
     */
    protected function next(): mixed{
        $readNext = $this->readNext++;
        if(!isset($this->toDecode[$readNext])) {
            $this->readNext--; return null;
        }
        return $this->toDecode[$readNext]["Value"]["raw"];
    }
}