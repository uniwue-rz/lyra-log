<?php
/**
* The tests for the logger are written here
*
* @author Pouyan Azari <pouyan.azari@uni-wuerzburg.de>
* @license MIT
*/

namespace De\Uniwue\RZ\Lyra\Log;

use De\Uniwue\RZ\Lyra\Log\Handler\LogHandlerFactory;

class BaseTest extends \PHPUnit_Framework_TestCase{

    public function setUp(){
        global $configRoot;
        global $logFile;
        $this->root = $configRoot;
        $this->logFile = $logFile;
    }

    /**
    * Test the init
    *
    */
    public function testInit(){
        $logger = new Logger("name");
        $this->assertEquals(get_class($logger), "De\Uniwue\RZ\Lyra\Log\Logger");
    }

    /**
    * Test the get name
    *
    */
    public function testGetName(){
        $logger = new Logger("name");
        $this->assertEquals($logger->getName(), "name");
    }

    /**
    * Tests the set name
    *
    **/
    public function testSetName(){
        $logger = new Logger("name");
        $logger->setName("old-name");
        $this->assertEquals($logger->getName(), "old-name");
    }

    /**
    * Tests the get handler
    *
    */
    public function testGetHandlers(){
        $logger = new Logger("name");
        $handler = new LogHandlerFactory("StdErr", "DEBUG");
        $logger->setHandlers(array($handler));
        $this->assertEquals(array($handler), $logger->getHandlers());
    }

    /**
    * Test the handler exception when it does not exists
    *
    * @expectedException De\Uniwue\RZ\Lyra\Exceptions\LogHandlerNotSupportedException
    */
    public function testGetHandlerException(){
        $logger = new Logger("name");
        $handler = new LogHandlerFactory("stdEEE", "DEBUG");
        $logger->setHandlers(array($handler));
    }

    /**
    * Tests the get logger method
    *
    */
    public function testGetLogger(){
        $logger = new Logger("name");
        $handler = new LogHandlerFactory("StdErr", "DEBUG");
        $logger->setHandlers(array($handler));
        $this->assertEquals(get_class($logger->getLogger()), "Monolog\Logger");
    }

    /**
    * Test the files written to log file
    *
    */
    public function testLogsToFile(){
        // Remove the existing file
        \unlink($this->logFile);
        $logger = new Logger("name");
        $handler = new LogHandlerFactory("Stream", "DEBUG", array("stream" =>$this->logFile));
        $logger->setHandlers(array($handler));
        $logger->getLogger()->info("TEST");
        $file = \file_get_contents($this->logFile);
        $this->assertContains("TEST", $file);
    }

    /**
    * Test the write to syslog
    *
    **/
    public function testLogsToSyslog(){
        $logger = new Logger("name");
        $handler = new LogHandlerFactory("Syslog", "DEBUG", array());
        $logger->addHandlers(array($handler));
        $logger->getLogger()->info("TEST-SYSLOG");
        $output = \exec('journalctl --since "1 sec ago"');
        $this->assertContains("TEST-SYSLOG", $output);
    }

    /**
    * Tests the logs to the std err
    *
    */
    public function testLogsToStdErr(){
        $logger = new Logger("name");
        $handler = new LogHandlerFactory("StdErr", "DEBUG");
        $logger->addHandler($handler);
        $logger->getLogger()->info("TEST-STD");
    }

    /**
    * Tests logs to stdOut
    *
    */
    public function testLogsToStdOut(){
        $logger = new Logger("name");
        $handler = new LogHandlerFactory("StdOut", "DEBUG");
        $logger->addHandler($handler);
        $logger->getLogger()->info("TEST-STD");       
    }

    /**
    * Tests logs to ErrorLog
    * Should normally show the logs in STDIO
    *
    */
    public function testLogsToErrorLog(){
        $logger = new Logger("name");
        $handler = new LogHandlerFactory("ErrorLog", "DEBUG");
        $logger->addHandler($handler);
        $logger->getLogger()->info("TEST-STD");   
    }

    /**
    * Test the logs with false options
    *
    * @expectedException De\Uniwue\RZ\Lyra\Exceptions\LogHandlerOptionNotExistsException
    */
    public function testLogsWithFalseOptions(){
        $logger = new Logger("name");
        $handler = new LogHandlerFactory("Stream", "DEBUG", array("streama" =>$this->logFile));
        $logger->setHandlers(array($handler));
        $logger->getLogger()->info("TEST");
        $file = \file_get_contents($this->logFile);
    }

    /**
    * Test the false levels
    *
    * @expectedException De\Uniwue\RZ\Lyra\Exceptions\LogLevelNotExistsException
    */
    public function testLogsWithFalseLevel(){
        $logger = new Logger("name");
        $handler = new LogHandlerFactory("Stream", "INFFO", array("stream" =>$this->logFile));
        $logger->setHandlers(array($handler));
        $logger->getLogger()->info("TEST");
        $file = \file_get_contents($this->logFile);
    }
}