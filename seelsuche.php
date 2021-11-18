<?php

define("AUTOLOADER_PATH", dirname(__FILE__) . '/vendor/autoload.php');
require AUTOLOADER_PATH;

use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;

use React\EventLoop\Loop;
use React\Socket\SecureServer;
use React\Socket\SocketServer;

use seelsuche\server\Server;

$options = getopt("", ["not-phar"]);
if(!isset($options["not-phar"])) {
    $self = new Phar(dirname(__FILE__));
    if(!file_exists(getWorkingDirectory() . '/data/'))
        mkdir("data");
    if(!file_exists(getWorkingDirectory() . '/data/config.json')) {
        $self->extractTo(getWorkingDirectory() . '/data', 'resources/default-config.json');
        rename(getWorkingDirectory() . '/data/resources/default-config.json', getWorkingDirectory() . '/data/config.json');
        rmdir(getWorkingDirectory() . "/data/resources");
    }
} else $self = null;

$config = json_decode(file_get_contents(
    getWorkingDirectory() . '/data/config.json'
), true);

# Define variables.
define("DEBUG", $config["development"]["enabled"]);
$encryption = $config["socket"];

$autoloader = new \BaseClassLoader();
$autoloader->register();

# Build web server.
$server = null;
if($encryption["ssl"] == true) {
    $loop = Loop::get();
    $socket = new SocketServer('0.0.0.0:' . $config["port"], [], $loop);
    $socket = new SecureServer($socket, $loop, [
        'local_cert'        => $encryption["certificate"], // path to your cert
        'local_pk'          => $encryption["privateKey"], // path to your server private key
        'allow_self_signed' => false,
        'verify_peer'       => false
    ]);

    try {
        $server = new IoServer(
            new HttpServer(new WsServer(
                new Server($autoloader, $config, $self)
            )), $socket, $loop
        );
    } catch (Exception $exception) {
        echo "Unable to start server: {$exception->getMessage()}\n"; die;
    }
} else {
    try {
        $server = IoServer::factory(
            new HttpServer(new WsServer(
                new Server($autoloader, $config, $self)
            )), $config["port"]
        );
    } catch (Exception $exception) {
        echo "Unable to start server: {$exception->getMessage()}\n"; die;
    }
}

# Run server.
$server->run();

function getWorkingDirectory(): string{
    return getcwd() . "/";
}