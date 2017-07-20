# Lyra Log
This is the log interface of the ***Lyra*** library. It is based on the `MonoLog` and can be customized to suite your needs. It contains two part: a Logger and a log handler. At the moment the handler supports the `Syslog`, PHP's `ErrorLog`, `Stdio` and `Stream` logging. The logger should work out of box which just creating the default handler and logger.

## Installation
**Lyra Log** is available in `packagist`, so you only need to add the package to your composer file to use it.

```bash
composer require rzuw/lyra-log
```

You can also clone or fork this repository in github and work with your own implementation:
```bash
git clone ssh://git@github.com/uniwue-rz/lyra-log.git
```
Then add it as local folder in composer:
```json
{
    "repositories": [
        {
            "type": "path",
            "url": "/path-to-git-clone"
        }
    ],
    "require": {
        "rzuw/lyra-log": "*"
    }
}
```
Or add directly from the git repository:
```json
{
    "require": {
        "rzuw/lyra-log": "*"
    },
    "repositories": [
        {
            "type": "vcs",
            "url":  "ssh://git@github.com/uniwue-rz/lyra-log.git"
        }
    ]
}
```

## Configuration
The configuration for the logger is very simple:

You need to create an instance of Logger with the given name. The name will always show up in the logs as `name.[LOG-LEVEL]`.  Then add the handlers to the instance so the data is logged is accordingly. The rest of the work is done by Monolog. More information can be found in usage example.

## Usage
You should include all the needed files, then create the logger and log handler. Afterwards you can
use it whenever you want.

```php
$logger = new Logger("name");
$handler = new LogHandlerFactory(
    "StdErr", "DEBUG", array("bubble"=>false));
$logger->setHandlers(array($handler));
$logger->getLogger()->info("This is an INFO message");
```

## Test
If you want to develop or debug this application you will need `phpunit` installed on your system. Then running:

```bash
composer install
phpunit
```
in root folder of this library will test the code.

## License
See LICENSE file.
