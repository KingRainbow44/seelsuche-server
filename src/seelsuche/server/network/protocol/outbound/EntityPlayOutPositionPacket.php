<?php

namespace seelsuche\server\network\protocol\outbound;

use JetBrains\PhpStorm\Pure;
use pocketmine\math\Vector3;
use seelsuche\server\network\protocol\OutboundPacket;

final class EntityPlayOutPositionPacket extends OutboundPacket
{
    /**
     * @var bool
     * True = The next value should be an entity runtime ID.
     * False = The next value is a player UID.
     */
    public bool $boundToEntity = false;
    public string|int $id;
    public float $xOffset, $yOffset, $zOffset, $modelPitch;

    protected function pid(): string
    {
        return self::ENTITY_PLAY_OUT_MOVEMENT;
    }

    public function encode(): string
    {
        $this->writeBool($this->boundToEntity);
        is_int($this->id) ? $this->writeInt($this->id) : $this->writeString($this->id);
        $this->writeFloat($this->xOffset);
        $this->writeFloat($this->yOffset);
        $this->writeFloat($this->zOffset);
        $this->writeFloat($this->modelPitch);

        return $this->prepare();
    }

    #[Pure] public static function create(bool $boundToEntity, string|int $id, Vector3 $position, float $pitch): self{
        $packet = new self();
        $packet->boundToEntity = $boundToEntity;
        $packet->id = $id;
        $packet->xOffset = $position->x;
        $packet->yOffset = $position->y;
        $packet->zOffset = $position->z;
        $packet->modelPitch = $pitch;
        return $packet;
    }
}