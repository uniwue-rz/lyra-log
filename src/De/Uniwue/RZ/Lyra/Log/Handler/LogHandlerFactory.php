<?php
/**
* This is the log handler class that should be used to create the log handler.
*
* @author Pouyan Azari <pouyan.azari@uni-wuerzburg.de>
* @license MIT
*/

namespace De\Uniwue\RZ\Lyra\Log\Handler;

use De\Uniwue\RZ\Lyra\Exceptions\LogLevelNotExistsException;
use De\Uniwue\RZ\Lyra\Exceptions\LogHandlerNotSupportedException;
use De\Uniwue\RZ\Lyra\Exceptions\LogHandlerOptionNotExistsException;

use Monolog\Handler\StreamHandler;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Handler\SyslogHandler;
use Monolog\Logger as MonologLogger;

class LogHandlerFactory{

    /**
    * The type of logger that should be used
    * @var string
    */
    private $handlerName;

    /**
    * level of the log that should be used
    *
    * @var string
    */
    private $level;

    /**
    * Specific options for the given logger
    * @var array
    */
    private $options;

    /**
    * The handler object
    * @var Handler
    */
    private $handler;

    /**
    * The formatter for the given logger
    * @var formatter
    */
    private $formatter;

    /**
    * Constructor
    *
    * @param string     $handlerName    The handler name
    * @param string     $level          The level of the log that should be used
    * @param array      $options        The options for the given log handler
    * @param Formatter  $formatter      The formatter for the given log handler
    */
    public function __construct($handlerName, $level = "DEBUG", $options = array(), $formatter = null){
        $this->handlerName = $handlerName;
        $this->level = $level;
        $this->options = $options;
        $this->formatter = $formatter;

        // check if the handler is supported
        if(\in_array($handlerName, $this->getSupportedHandlers()) === false){

            throw new LogHandlerNotSupportedException("The given handler is not supported");
        }

        // check if the given option is valid
        if(sizeof(\array_diff_key($options, $this->getHandlerDefaultOptions($handlerName))) > 0){

            throw new LogHandlerOptionNotExistsException("The option for the handler does not exists");
        }

        $this->createHandler();
    }

    /**
    * Creates the handler a new
    */
    public function createHandler(){
        // Fallbacks not set options to have the complete options array
        $this->setOptions($this->fallbackOptions($this->handlerName, $this->options));
        // Dispatch the handlerName to the right handler
        $this->setHandler($this->handlerDispatcher($this->handlerName, $this->level, $this->options));
        if($this->formatter !== null){
            $this->handler->setFormatter($this->formatter);
        }
    }

    /**
    * Sets the options for the given logger
    *
    * @param array $options The option for the given system
    */
    public function setOptions($options =array()){
        $this->options = $options;
    }

    /**
    * Returns the options for the given logHandler
    *
    * @return array
    */
    public function getOptions(){

        return $this->options;
    }

    /**
    * Handler dispatcher, that is used to dispatch the handler and options to the right system
    *
    * @param string $handlerName The name of the given handler
    * @param string $level       The log level
    * @param array  $options     The options array for the given handler
    *
    * @return Handler
    **/
    public function handlerDispatcher($handlerName, $level, $options = array()){
        switch($handlerName){

            case "StdErr":
                return $this->createStdErrHandler($level, $options);
                break;

            case "StdOut":
                return $this->createStdOutHandler($level, $options);
                break;

            case "Stream":
                return $this->createStreamHandler($level, $options);
                break;

            case "Syslog":
                return $this->createSyslogHandler($level, $options);
                break;

            case "ErrorLog":
                return $this->createErrorLogHandler($level, $options);
                break;
        }
    }

    /**
    * Returns the handler for the given system
    *
    *
    */
    public function getHandler(){

        return $this->handler;
    }

    /**
    * Sets the handler for the given logger
    *
    * @param Handler $handler The handler for the 
    */
    public function setHandler($handler){
        $this->handler = $handler;
    }

    /**
    * Fallbacks the options for the given handler
    *
    * @param string $handlerName The handler name for the given logger
    * @param array  $options     The options for the given handler
    *
    * @return array
    */
    public function fallbackOptions($handlerName, $options = array()){
        
        return \array_merge($this->getHandlerDefaultOptions($handlerName), $options);
    }

    /**
    * Create the StrErr handler from the given level and options
    *
    * @param string $level The level of logging 
    * @param array  $options     The options for the given handler
    *
    * @return StreamHandler
    */
    public function createStdErrHandler($level, $options = array()){

        return $this->createStreamHandler($level, $options);
    }

    /**
    * Creates the StdOut handler for the given the given logger
    *
    * @param string $level The level of logging
    * @param array  $options     The options for the given handler
    *
    * @return  StreamHandler
    */
    public function createStdOutHandler($level, $options = array()){

        return $this->createStreamHandler($level, $options);
    }

    /**
    * Creates the ErrorLog hander for the given logger.The messageType can
    * be 0 for OS or 4 for SAPI.
    *
    * @param string $level      The log level
    * @param array  $options    The options for the given logger
    */
    public function createErrorLogHandler($level, $options = array()){

        return new ErrorLogHandler(
            $options["messageType"],
            $this->mapLevel($level),
            $options["bubble"],
            $options["expandNewLines"]
            );
    }

    /**
    * Create Syslog handler from the given options
    *
    * @param string $level      The log level
    * @param array  $options    The options for the log handler
    */
    public function createSyslogHandler($level, $options = array()){

        return new SyslogHandler(
            $options["ident"],
            $options["facility"],
            $this->mapLevel($level),
            $options["bubble"],
            $options["logopts"]
        );
    }

    /**
    * Create Stream handler from the given options
    *
    * @param string $level The string options for the given log
    * @param array  $options The option for the given log
    *
    * @return StreamHandler
    */
    public function createStreamHandler($level, $options = array()){

        return new StreamHandler(
                $options["stream"],
                $this->mapLevel($level),
                $options["bubble"],
                $options["filePermission"],
                $options["useLocking"]
        );
    }

    /**
    * Returns the default options for the given handler name
    *
    * @param string $handlerName The name of the handler that should be used
    *
    * @return array
    */
    public function getHandlerDefaultOptions($handlerName){
        switch($handlerName){
            case "StdErr":

                return $this->getStdErrDefaultOptions();
                break;

            case "StdOut":

                return $this->getStdOutDefaultOptions();
                break;

            case "Syslog":
                
                return $this->getSyslogDefaultOptions();
                break;

            case "ErrorLog":

                return $this->getErrorLogDefaultOptions();
                break;

            case "Stream":

                return $this->getStreamDefaultOptions();
                break;

            default:

                return $this->getStreamDefaultOptions();
                break;
        }
    }

    /**
    * Returns the error log default options
    *
    * @return array
    */
    public function getErrorLogDefaultOptions(){

        return array(
            "messageType" => 0,
            "bubble" => false,
            "expandNewLines" => true
        );
    }

    /**
    * Returns the Syslog default options
    *
    * @return array
    */
    public function getSyslogDefaultOptions(){

        return array(
            "ident" => "lyra",
            "facility" => LOG_USER,
            "bubble" => true,
            "logopts"=> LOG_PID
        );
    }

    /**
    * Returns the default options for the stream handler
    *
    * @return array
    */
    public function getStreamDefaultOptions(){

        return array(
            "stream" => "lyra.log",
            "bubble" => true,
            "filePermission" => null,
            "useLocking" => false
        );
    }

    /**
    * Returns the default options for the stdErr
    *
    * @return array
    */
    public function getStdErrDefaultOptions(){

        $options = $this->getStreamDefaultOptions();
        $options["stream"] = "php://stderr";

        return $options;
    }

    /**
    * Returns the default options for the stdOut
    *
    * @return array
    */
    public function getStdOutDefaultOptions(){

        $options = $this->getStreamDefaultOptions();
        $options["stream"] = "php://stdout";

        return $options;    
    }

    /**
    * Returns the list of supported handlers for the log handler
    *
    * @return array
    */
    public function getSupportedHandlers(){

        return array(
            "Syslog",
            "ErrorLog",
            "StdErr",
            "StdOut",
            "Stream",
        );
    }
    /**
    * Returns the level for the given logger
    *
    * @return string
    */
    public function getLevel(){

        return $this->level;
    }

    /**
    * Sets the level of the logging in the logger
    *
    * @param string $level The level the logging should be done
    */
    public function setLevel($level){
        $this->level = $level;
    }

    /**
    * Returns the map of log levels
    *
    * @return array
    **/
    public function getLogLevelMap(){
        $map = array(
            "DEBUG" => MonologLogger::DEBUG,
            "INFO" => MonologLogger::INFO,
            "NOTICE" => MonologLogger::NOTICE,
            "WARNING" => MonologLogger::WARNING,
            "ERROR" =>  MonologLogger::ERROR,
            "CRITICAL" => MonologLogger::CRITICAL,
            "ALERT" => MonologLogger::ALERT,
            "EMERGENCY" => MonologLogger::EMERGENCY
        );

        return $map;
    }

    /**
    * Maps the string level to the Logger values
    *
    * @param string $level The level that the corresponding number should be find
    *
    * @return int
    */
    public function mapLevel($level){
        if(isset($this->getLogLevelMap()[$level]) === false){

            throw new LogLevelNotExistsException("The given log level does not exists");
        }

        return $this->getLogLevelMap()[$level];
    }

    /**
    * Sets the handlerName for the given Handler
    *
    * @param string $handlerName The name of the handler
    */
    public function setHandlerName($handlerName){
        $this->handlerName = $handlerName;
    }

    /**
    * Returns the handlerName for the given Handler
    *
    * @return string
    */
    public function getHandlerName(){

        return $this->handlerName;
    }

    /**
    * Returns the formatter for the given log handler
    *
    * @return Formatter
    */
    public function getFormatter(){
        
        return $this->formatter;
    }

    /**
    * Sets the formatter for the given log handler
    *
    * @param Formatter $formatter The formatter for the given logger
    */
    public function setFormatter($formatter){
        $this->formatter = $formatter;
    }
}