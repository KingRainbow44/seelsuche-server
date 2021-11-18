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
    public function next(): mixed{
        if(!isset($this->toDecode[$this->readNext++])) {
            $this->readNext--; return null;
        }
        return $this->toDecode[$this->readNext++]["Value"]["raw"];
    }
}