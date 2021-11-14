<?php

namespace seelsuche\server\entity;

final class EntityManager
{
    private static int $nextId = 0;
    /** @var Entity[] */
    private static array $entities = [];

    /**
     * Returns the entity ID given to the entity as well as
     * registering the entity.
     */
    public static function getNextId(Entity $entity): int{
        self::$entities[($id = self::$nextId++)] = $entity;
        return $id;
    }

    public static function getEntityById(int $id): ?Entity{
        return self::$entities[$id] ?? null;
    }
}