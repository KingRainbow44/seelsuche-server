<?php

final class Logger {
    public static function debug(string $message) {
        if(true)
            echo "[debug] $message\n";
    }

    public static function info(string $message) {
        echo "[info] $message\n";
    }

    public static function warning(string $message) {
        echo "[warning] $message\n";
    }

    public static function critical(string $message) {
        echo "[CRITICAL] $message\n";
    }
}

Logger::info("a");

function getWorkingDirectory(): string{
    return getcwd() . "/";
}