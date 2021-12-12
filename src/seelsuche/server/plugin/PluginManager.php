<?php

namespace seelsuche\server\plugin;

use seelsuche\server\plugin\events\EventManager;

final class PluginManager
{
    private array $plugins = [];

    private EventManager $eventManager;

    public function __construct()
    {
        $this->eventManager = new EventManager();
    }

    public function registerPlugin(Plugin $plugin): void
    {
        $this->plugins[$plugin->getUniqueId()] = $plugin;
        $plugin->onLoad();
    }

    public function enablePlugins(): void{
        foreach($this->plugins as $plugin) {
            $plugin->onEnable();
        }
    }

    public function disablePlugins(): void{
        foreach($this->plugins as $plugin) {
            $plugin->onDisable();
        }
    }

    public function getEventManager(): EventManager
    {
        return $this->eventManager;
    }
}