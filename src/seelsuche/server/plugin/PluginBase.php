<?php

namespace seelsuche\server\plugin;

use seelsuche\server\Server;

abstract class PluginBase
{
    private Server $server;

    private bool $enabled = false;
    private ?string $pluginId = null;

    public function __construct(Server $server) {
        $this->server = $server;
    }

    /**
     * Events.
     */

    protected function onLoad(): void {}
    protected function onEnable(): void {}
    protected function onDisable(): void {}

    /**
     * Final Methods
     */

    public final function getServer(): Server{
        return $this->server;
    }

    /**
     * Returns a unique plugin identifier.
     */
    public final function getUniqueId(): string{
        return $this->pluginId ?? $this->pluginId = strval(mt_rand(111111, 999999));
    }
}