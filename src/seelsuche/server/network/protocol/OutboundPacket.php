<?php

namespace seelsuche\server\network\protocol;

use Exception;
use seelsuche\server\network\OutboundPacketIds;

abstract class OutboundPacket extends Packet implements OutboundPacketIds
{
    /**
     * @throws Exception
     */
    public function next(): object
    {
        throw new Exception("Unable to call `next()` on an OutboundPacket.");
    }

    private function write(string $type, $data): void{
        $this->toEncode[$this->writeNext++] = ["type" => $type, "raw" => $data];
    }

    protected function writeInt(int $data): void{
        $this->write("int", $data);
    }

    protected function writeString(string $data): void{
        $this->write("string", $data);
    }

    protected function writeBool(bool $data): void{
        $this->write("boolean", $data);
    }

    protected function writeFloat(float $data): void{
        $this->write("float", $data);
    }

    protected function prepare(): string{
        return json_encode([
            "request" => "packet",
            "data" => $this->toEncode,
            "id" => $this->pid()
        ]);
    }

    public abstract function encode(): string;
}