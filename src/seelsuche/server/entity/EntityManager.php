<?php

namespace seelsuche\server\entity;

final class EntityManager
{
    private static int $nextId = 0;

    public static function getNextId(): int{
        return self::$nextId++;
    }
}