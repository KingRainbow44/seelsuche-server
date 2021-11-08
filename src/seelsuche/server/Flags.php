<?php

namespace seelsuche\server;

/**
 * A wrapper for the Seelsuche config.
 */
final class Flags
{
    private array $config;

    /**
     * @param array $config The config to read from.
     */
    public function __construct(array $config) {
        $this->config = $config;
    }

    /**
     * Returns the server's listening port.
     */
    public function getPort(): string{
        return $this->config["port"] ?? "8443";
    }

    /**
     * Returns TRUE when the server owner has manually enabled debug mode.
     * Returns FALSE when the mode is not set or isn't available.
     */
    public function isDebugEnabled(): bool{
        return boolval($this->config["development"]["enabled"] ?? "false");
    }
}