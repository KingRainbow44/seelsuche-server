<?php /** @noinspection PhpPossiblePolymorphicInvocationInspection */

/** @noinspection PhpUndefinedFieldInspection */
/** @noinspection PhpMultipleClassesDeclarationsInOneFile */

namespace seelsuche\server;

use ClassLoader;
use Exception;
use Phar;
use SplObjectStorage;

use JetBrains\PhpStorm\NoReturn;

use seelsuche\server\network\DefaultProtocolInterface;
use seelsuche\server\network\PacketManager;
use seelsuche\server\network\ProtocolInterface;
use seelsuche\server\player\PlayerManager;
use seelsuche\server\utils\Database;
use seelsuche\server\plugin\PluginManager;
use const seelsuche\WORKING_DIRECTORY;

use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

final class Server implements MessageComponentInterface
{
    private static ?Server $instance = null;

    private PluginManager $pluginManager;
    private ?Database $database = null;
    private SplObjectStorage $clients;
    private ClassLoader $autoloader;
    private ?Phar $pharFile;
    private Flags $flags;

    private array $config;

    public ProtocolInterface $protocolInterface;

    /**
     * @throws Exception
     */
    public function __construct(ClassLoader $autoloader, array $config, ?Phar $pharFile)
    {
        if(self::$instance != null)
            throw new Exception("Another server instance is already defined.");
        self::$instance = $this; $startTime = time();
        $this->autoloader = $autoloader;

        $this->clients = new SplObjectStorage();
        $this->pluginManager = new PluginManager();

        $this->config = $config; $this->flags = new Flags($config);
        $this->database = new Database($config["database"]); $this->initializeDatabase();
        $this->pharFile = $pharFile;

        # Set default protocol interface & initialize packet manager.
        $this->protocolInterface = new DefaultProtocolInterface();
        PacketManager::initialize();

        $time = round(time() - $startTime, 3);
        Logger::info("All done! Server started in {$time}s");
    }

    /**
     * Returns the server instance.
     */
    public static function getInstance(): ?Server{
        return self::$instance;
    }

    /**
     * Returns the current protocol interface. Can be changed by plugins.
     */
    public static function getProtocolInterface(): ProtocolInterface{
        return self::$instance->protocolInterface;
    }

    /*
     * Server Functions
     */

    /**
     * Shuts down the server, returning an exit code of 0.
     */
    #[NoReturn] public function shutdown(): void{
        # Kick players out of server.
        foreach($this->clients as $client) {
            assert($client instanceof ConnectionInterface);
            $client->send('error:server-shutdown');
        }

        # Close MySQL connection.
        $this->database?->close();

        # Terminate the process.
        exit(0);
    }

    /**
     * @return Flags|array A multi-dimensional array containing all the keys & values of the JSON config.
     * The config file is located in '/data/config.json'.
     */
    public function getConfig(bool $returnFlags = true): Flags|array{
        return $returnFlags ? $this->flags : $this->config;
    }

    /**
     * @return ?Phar This is the 'Phar' instance of the file.
     * Returns the file, created when the server is initialized.
     */
    public function getFile(): ?Phar{
        return $this->pharFile;
    }

    /**
     * @return Database The database instance for the server.
     * Used for running `query()` on the database defined in the config.
     */
    public function getDatabase(): Database{
        return $this->database;
    }

    /**
     * @return PluginManager The plugin manager for this server.
     *
     */
    public function getPluginManager(): PluginManager{
        return $this->pluginManager;
    }

    /**
     * @return ClassLoader The class autoloader for threads.
     */
    public function getAutoLoader(): ClassLoader{
        return $this->autoloader;
    }

    /*
     * Socket Functions
     */

    public function onOpen(ConnectionInterface $conn)
    {
        Logger::debug("Connection made with $conn->remoteAddress");

        # Register player to manager.
        if(PlayerManager::getPlayerByUserId($conn->resourceId) == null)
            PlayerManager::addPlayer($conn->resourceId, $conn->remoteAddress, $conn);
        else {
            PlayerManager::removePlayer($conn->resourceId);
            PlayerManager::addPlayer($conn->resourceId, $conn->remoteAddress, $conn);
        }
        # Add the connection to the array.
        $this->clients->attach($conn);
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        if(!isJson($msg)) {
            $from->send('error:invalid-json'); return;
        } $data = json_decode($msg);
        if($data[0] == null) {
            $from->send('error:invalid-request'); return;
        }

        # Get client from 'resourceId'.
        $client = PlayerManager::getPlayer($from->resourceId);

        switch($data[0]) {
            default:
                $from->send('error:not-handled');
                break;
            case "packet":
                PacketManager::receivePacket($msg, $client);
                return;
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        Logger::debug("Connection closed with $conn->remoteAddress");

        # Unregister the player from the manager.
        PlayerManager::removePlayer($conn->resourceId);
        # Remove the connection from the array.
        $this->clients->detach($conn);
    }

    public function onError(ConnectionInterface $conn, Exception $e)
    {
        Logger::warning("Websocket Error. {$e->getMessage()}");
        $this->onClose($conn); # Call again in-case it isn't called when an error occurs.
    }

    /*
     * Private Methods
     */

    private function initializeDatabase(): void{
        $database = $this->database;
        try {
            $database->query("CREATE TABLE IF NOT EXISTS `users` ( `userId` VARCHAR(10) NOT NULL , `username` tinytext NOT NULL , `hash` TEXT NOT NULL );");
            $database->query("CREATE TABLE IF NOT EXISTS `data` ( `userId` VARCHAR(10) NOT NULL , `data` LONGTEXT NOT NULL );");
        } catch (Exception) {
            Logger::critical("Failed to initialize database.");
            $this->shutdown();
        }
    }

    /**
     * Create server-related files and directories.
     */
    private function initializeFileSystem(): void{
        $workingDirectory = WORKING_DIRECTORY . DIRECTORY_SEPARATOR;

        # Make directories.
        if(!file_exists($workingDirectory . "plugins"))
            @mkdir($workingDirectory . "plugins");
        # Make files.
        # The configuration is made in the entry point.
//        if(!file_exists($workingDirectory . "config.json"))
//            @touch($workingDirectory . "config.json");
        if(!file_exists($workingDirectory . "server.log"))
            @touch($workingDirectory . "server.log");
    }
}

final class Logger {
    public static function debug(string $message) {
        if(Server::getInstance()->getConfig()->isDebugEnabled())
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

function isJson(string $data): bool{
    json_decode($data); return json_last_error() === JSON_ERROR_NONE;
}