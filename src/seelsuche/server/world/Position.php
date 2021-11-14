<?php

namespace seelsuche\server\world;

use pocketmine\math\Vector3;

class Position
{
    protected Vector3 $position;
    protected int $world;

    public function __construct(float $x = 0.0, float $y = 0.0, float $z = 0.0, int $world = WorldType::GAME_WORLD) {
        $this->position = new Vector3($x, $y, $z);
        $this->world = $world;
    }

    public final function getX(): float{
        return $this->position->x;
    }

    public final function getY(): float{
        return $this->position->y;
    }

    public final function getZ(): float{
        return $this->position->z;
    }

    public final function getWorld(): int{
        return $this->world;
    }

    public final function setX(float $x): void{
        $this->position->x = $x;
    }

    public final function setY(float $y): void{
        $this->position->y = $y;
    }

    public final function setZ(float $z): void{
        $this->position->z = $z;
    }

    public final function setWorld(int $world): void{
        $this->world = $world;
    }
}