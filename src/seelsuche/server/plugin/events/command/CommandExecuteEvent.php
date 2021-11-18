<?php

namespace seelsuche\server\plugin\events\command;

use seelsuche\server\player\Player;
use seelsuche\server\plugin\events\Event;
use seelsuche\server\plugin\events\types\Cancellable;

class CommandExecuteEvent extends Event implements Cancellable
{
    private ?Player $executor;
    private string $rawCommand;

    public function __construct(?Player $executor, string $rawCommand) {
        $this->executor = $executor;
        $this->rawCommand = $rawCommand;
    }

    public function getExecutor(): ?Player{
        return $this->executor;
    }

    public function getRawCommand(): string{
        return $this->rawCommand;
    }
}