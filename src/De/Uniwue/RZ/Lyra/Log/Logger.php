<?php
/**
* The logger class for the lyra application. It is based on monolog and can log to different interfaces
* on the fly.
*
* @author Pouyan Azari <pouyan.azari@uni-wuerzbrug.de>
* @license MIT
*/

namespace De\Uniwue\RZ\Lyra\Log;

use Monolog\Logger as MonologLogger;

class Logger{
    
    /**
    * Placeholder for the logger name
    *
    * @var string
    */
    private $name;

    /**
    * Placeholder for the log handlers
    *
    * @var array
    */
    private $handlers;

    /**
    * Placeholder for the logger
    *
    * @var MonologLogger
    */
    private $logger;

    /**
    * Constructor
    *
    * @param string $name The name of the given string
    * @param array  $handlers The handlers for the given logger
    * @param bool   $micro    Should monolog use microseconds timestamp (disable it for performance)
    */
    public function __construct($name, $handlers = array(), $micro = false){
        $this->name = $name;
        $this->handlers = $handlers;
        $this->logger = new MonologLogger($this->name);
        foreach($handlers as $handler){
            $this->logger->pushHandler($handler->getHandler());
        }
        $this->logger->useMicrosecondTimestamps($micro);
    }

    /**
    * Adds a single handler to the list of handlers
    *
    * @param Handler $handler The handler that should be added
    */
    public function addHandler($handler){
        $this->addHandlers(array($handler));
        $this->logger->pushHandler($handler->getHandler());
    }

    /**
    * Add an array of handlers to the list of existing one
    *
    * @param array $handlers The handlers array
    **/
    public function addHandlers($handlers = array()){
        $this->handlers = \array_merge($this->handlers, $handlers);
        // Push the the handlers to the logger
        foreach($handlers as $handler){
            $this->logger->pushHandler($handler->getHandler());
        }
    }

    /**
    * Sets the handlers a new
    *
    * @param Handler $handler The handler that should be set
    */
    public function setHandlers($handlers){
        $this->handlers = $handlers;
        // remove the existing handlers
        foreach($this->logger->getHandlers() as $handler){
                $this->logger->popHandler();
        }
        // Push the new handlers
        foreach($handlers as $handler){
            $this->logger->pushHandler($handler->getHandler());
        }       
    }

    /**
    * Returns the list of handlers for the given logger
    *
    * @return array
    */
    public function getHandlers(){

        return $this->handlers;
    }

    /**
    * Configures and returns the given logger
    *
    * @return Logger
    **/
    public function getLogger(){
        return $this->logger;
    }

    /**
    * Set the logger 
    *
    * @param Logger $logger The logger for the given application
    */
    public function setLogger($logger){
        $this->logger = $logger;
    }

    /**
    * Returns the name of the given logger
    *
    * @return string
    */
    public function getName(){

        return $this->name;
    }

    /**
    * Set the name of the given logger
    *
    * @param string $name The name of the logger
    **/
    public function setName($name){
        $this->name = $name;
    }

    /**
     * Adds a log record at an arbitrary level.
     *
     * This method allows for compatibility with common interfaces.
     *
     * @param mixed  $level   The log level
     * @param string $message The log message
     * @param array  $context The log context
     */
    public function log($level, $message, array $context = [])
    {
        $this->getLogger()->log($level, $message, $context);
    }

    /**
     * Adds a log record at the DEBUG level.
     *
     * This method allows for compatibility with common interfaces.
     *
     * @param string $message The log message
     * @param array  $context The log context
     */
    public function debug($message, array $context = [])
    {
        $this->getLogger()->debug($message, $context);
    }
    /**
     * Adds a log record at the INFO level.
     *
     * This method allows for compatibility with common interfaces.
     *
     * @param string $message The log message
     * @param array  $context The log context
     */
    public function info($message, array $context = [])
    {
        $this->getLogger()->info($message, $context);
    }
    /**
     * Adds a log record at the NOTICE level.
     *
     * This method allows for compatibility with common interfaces.
     *
     * @param string $message The log message
     * @param array  $context The log context
     */
    public function notice($message, array $context = [])
    {
        $this->getLogger()->notice($message, $context);
    }
    /**
     * Adds a log record at the WARNING level.
     *
     * This method allows for compatibility with common interfaces.
     *
     * @param string $message The log message
     * @param array  $context The log context
     */
    public function warning($message, array $context = [])
    {
        $this->getLogger()->warning($message, $context);
    }
    /**
     * Adds a log record at the ERROR level.
     *
     * This method allows for compatibility with common interfaces.
     *
     * @param string $message The log message
     * @param array  $context The log context
     */
    public function error($message, array $context = [])
    {
        $this->getLogger()->error($message, $context);
    }
    /**
     * Adds a log record at the CRITICAL level.
     *
     * This method allows for compatibility with common interfaces.
     *
     * @param string $message The log message
     * @param array  $context The log context
     */
    public function critical($message, array $context = [])
    {
        $this->getLogger()->critical($message, $context);
    }
    /**
     * Adds a log record at the ALERT level.
     *
     * This method allows for compatibility with common interfaces.
     *
     * @param string $message The log message
     * @param array  $context The log context
     */
    public function alert($message, array $context = [])
    {
        $this->getLogger()->alert($message, $context);
    }
    /**
     * Adds a log record at the EMERGENCY level.
     *
     * This method allows for compatibility with common interfaces.
     *
     * @param string $message The log message
     * @param array  $context The log context
     */
    public function emergency($message, array $context = [])
    {
        $this->getLogger()->emergency($message, $context);
    }
}