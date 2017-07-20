<?php
/**
* This is the exception that is called when the given log level does not exists
*
* @author Pouyan Azari <pouyan.azari@uni-wuerzburg.de>
* @license MIT
*/

namespace De\Uniwue\RZ\Lyra\Exceptions;

class LogLevelNotExistsException extends \Exception{

    /**
    * Constructor
    *
    * @param string         $message        The message of the given exception
    * @param int            $code           The code of the given exception
    * @param \Exception     $previous       The previous Exception (When chained)
    */
    public function __construct($message, $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
    
    /**
    * Generic toString Method
    *
    * @return string
    */
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}