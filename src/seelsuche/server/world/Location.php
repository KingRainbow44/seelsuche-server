<?php

namespace seelsuche\server\world;

class Location extends Position
{
    private float $pitch, $yaw;

    public function __construct(float $x = 0.0, float $y = 0.0, float $z = 0.0,
                                float $pitch = 0.0, float $yaw = 0.0, int $world = WorldType::GAME_WORLD)
    {
        parent::__construct($x, $y, $z, $world);
        $this->pitch = $pitch;
        $this->yaw = $yaw;
    }

    public final function getPitch(): float{
        return $this->pitch;
    }

    public final function getYaw(): float{
        return $this->yaw;
    }

    public final function setPitch(float $pitch): void{
        $this->pitch = $pitch;
    }

    public final function setYaw(float $yaw): void{
        $this->yaw = $yaw;
    }
}