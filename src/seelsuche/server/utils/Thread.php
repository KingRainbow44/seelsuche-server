<?php

namespace seelsuche\server\utils;

use ClassLoader;
use seelsuche\server\Server;

abstract class Thread extends \Thread
{
    /** @var ClassLoader|null */
    protected ?ClassLoader $classLoader;

    /** @var bool */
    protected bool $isKilled = false;

    /**
     * @return ClassLoader|null
     */
    public function getClassLoader(): ?ClassLoader{
        return $this->classLoader;
    }

    /**
     * @return void
     */
    public function setClassLoader(ClassLoader $loader = null): void{
        if($loader === null){
            $loader = Server::getInstance()->getAutoLoader();
        }
        $this->classLoader = $loader;
    }

    /**
     * Registers the class loader for this thread.
     *
     * WARNING: This method MUST be called from any descendent threads' run() method to make autoloading usable.
     * If you do not do this, you will not be able to use new classes that were not loaded when the thread was started
     * (unless you are using a custom autoloader).
     *
     * @return void
     */
    public function registerClassLoader(): void{
        $this->classLoader?->register(false);
    }

    /**
     * @param int $options
     * @return bool
     */
    public function start(int $options = PTHREADS_INHERIT_ALL): bool{
        if($this->getClassLoader() === null){
            $this->setClassLoader();
            $this->registerClassLoader();
        }

        return parent::start($options);
    }

    /**
     * Stops the thread using the best way possible. Try to stop it yourself before calling this.
     *
     * @return void
     */
    public function quit(){
        $this->isKilled = true;

        if(!$this->isJoined()){
            $this->notify();
            $this->join();
        }
    }

    public function getThreadName() : string{
        return (new \ReflectionClass($this))->getShortName();
    }
}