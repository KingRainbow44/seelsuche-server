<?php

namespace seelsuche\server\utils;

use Exception;
use mysqli;

use seelsuche\server\Logger;
use seelsuche\server\Server;

final class Database
{
    private mysqli $database;

    public function __construct(array $databaseEntry) {
        $this->database = new mysqli($databaseEntry["address"], $databaseEntry["username"],
            $databaseEntry["password"], $databaseEntry["database"], $databaseEntry["port"]);
        if(mysqli_connect_error() !== null) {
            Logger::critical("Unable to connect to MySQL database. Please check your config and restart the server.");
            Server::getInstance()->shutdown();
        } else {
            Logger::info("Connected to MySQL database.");
        }
    }

    /**
     * @throws Exception
     */
    public function query(string $query, callable $callback = null): void{
        if(!$this->database->ping()) {
            Logger::critical("The database has stopped responding at: " . time());
            throw new Exception("The database has stopped responding at: " . time());
        }

        $results = $this->database->query($query);
        if(is_bool($results) && $results === false) {
            throw new Exception("An error occurred. Query: $query Error: {$this->database->error}");
        }

        $rows = [];
        if(!is_bool($results))
            while(($row = mysqli_fetch_assoc($results)))
                $rows[] = $row;

        if($callback != null)
            $callback($rows);
    }
}