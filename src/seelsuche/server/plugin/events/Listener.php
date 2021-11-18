<?php

namespace seelsuche\server\plugin\events;

interface Listener
{
    public function onEvent(Event $event, $eventClass);
}