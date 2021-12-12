<?php

namespace seelsuche\server\plugin;

use seelsuche\server\Server;

interface Plugin
{
    function onLoad(): void;
    function onEnable(): void;
    function onDisable(): void;

    function getServer(): Server;
    function getUniqueId(): string;
}