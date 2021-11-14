<?php

namespace seelsuche\server\entity;

use seelsuche\server\world\Location;

/**
 * Server-sided entity class.
 * Should not be used for AI-based entities.
 */
abstract class Entity extends Location
{
    # Internal variables.
    protected int $entityId = -1;

    public function __construct()
    {
        parent::__construct();
        $this->entityId = EntityManager::getNextId($this);
    }

    /**
     * Get the internal entity id. Used in the entity manager.
     */
    public final function getEntityId(): int{
        return $this->entityId;
    }
}