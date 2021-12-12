<?php

namespace seelsuche\server\plugin\events;

use seelsuche\server\plugin\events\types\Cancellable;
use seelsuche\server\Server;

abstract class Event
{
    private bool $canceled = false;

    public function setCanceled(bool $canceled = true): void{
        if(!$this instanceof Cancellable)
            throw new \RuntimeException("Cannot cancel event that isn't declared as cancellable.");
        $this->canceled = $canceled;
    }

    public function isCancelled(): bool{
        return $this->canceled;
    }

    public function call(): void{
        Server::getInstance()->getPluginManager()->getEventManager()->callEvent($this);
    }
}