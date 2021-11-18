<?php

namespace seelsuche\server\plugin\events;

final class EventManager
{
    /** @var Listener[] */
    private array $listeners = [];

    public function registerListener(Listener $listener) {
        $this->listeners[] = $listener;
    }

    public function callEvent(Event $event) {
        foreach($this->listeners as $listener)
            $listener->onEvent($event, get_class($event));
    }
}