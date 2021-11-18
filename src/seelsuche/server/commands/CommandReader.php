<?php

namespace seelsuche\server\commands;

use JetBrains\PhpStorm\Pure;
use Threaded;

final class CommandReader extends \Thread
{
    public const TYPE_READLINE = 0;
    public const TYPE_STREAM = 1;
    public const TYPE_PIPED = 2;

    /** @var resource */
    private static $stdin;

    /** @var Threaded */
    protected Threaded $buffer;
    /** @var bool */
    private bool $shutdown = false;
    /** @var int */
    private int $type = self::TYPE_STREAM;

    #[Pure] public function __construct() {
        $this->buffer = new Threaded();
    }

    public function shutdown(): void{
        $this->shutdown = true;
    }

    /**
     * @throws \Exception
     */
    public function quit(): void{
        $wait = microtime(true) + 0.5;
        while(microtime(true) < $wait){
            if($this->isRunning()){
                usleep(100000);
            }else{
                return;
            }
        }

        $message = "Thread blocked for unknown reason";
        if($this->type === self::TYPE_PIPED){
            $message = "STDIN is being piped from another location and the pipe is blocked, cannot stop safely";
        }

        throw new \Exception($message);
    }

    private function initStdin() : void{
        if(is_resource(self::$stdin)){
            fclose(self::$stdin);
        }

        self::$stdin = fopen("php://stdin", "r");
        if($this->isPipe(self::$stdin)){
            $this->type = self::TYPE_PIPED;
        }else{
            $this->type = self::TYPE_STREAM;
        }
    }

    /**
     * Checks if the specified stream is a FIFO pipe.
     *
     * @param resource $stream
     */
    private function isPipe($stream) : bool{
        return is_resource($stream) and (!stream_isatty($stream) or ((fstat($stream)["mode"] & 0170000) === 0010000));
    }

    /**
     * Reads a line from the console and adds it to the buffer. This method may block the thread.
     *
     * @return bool if the main execution should continue reading lines
     */
    private function readLine() : bool{
        if(!is_resource(self::$stdin)){
            $this->initStdin();
        }

        $r = [self::$stdin];
        $w = $e = null;
        if(($count = stream_select($r, $w, $e, 0, 200000)) === 0){ //nothing changed in 200000 microseconds
            return true;
        }elseif($count === false){ //stream error
            $this->initStdin();
        }

        if(($raw = fgets(self::$stdin)) === false){ //broken pipe or EOF
            $this->initStdin();
            $this->synchronized(function() : void{
                $this->wait(200000);
            }); //prevent CPU waste if it's end of pipe
            return true; //loop back round
        }

        $line = trim($raw);

        if($line !== ""){
            $this->buffer[] = preg_replace("#\\x1b\\x5b([^\\x1b]*\\x7e|[\\x40-\\x50])#", "", $line);
        }

        return true;
    }

    /**
     * Reads a line from console, if available. Returns null if not available
     *
     * @return string|null
     */
    public function getLine(){
        if($this->buffer->count() !== 0){
            return (string) $this->buffer->shift();
        }

        return null;
    }

    /**
     * @return void
     */
    public function run(){
        $this->initStdin();

        /** @noinspection PhpStatementHasEmptyBodyInspection */
        while(!$this->shutdown and $this->readLine());

        fclose(self::$stdin);
    }

    public function getThreadName() : string{
        return "Console";
    }
}