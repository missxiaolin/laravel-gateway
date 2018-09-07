<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 2018/9/8
 * Time: 上午6:43
 */

namespace Lin\Support\Logger;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

/**
 * Class Factory
 * @package Lin\Support\Logger
 */
class Factory implements FactoryInterface
{
    /**
     * @var array
     */
    public static $instances = [];

    /**
     * @param $name
     * @return Logger
     */
    private function getClient($name)
    {
        $monolog = new Logger('factory');

        $log_path = storage_path('logs') . '/' . $name . '.log';

        $handler = new RotatingFileHandler($log_path, 5, Logger::DEBUG, true, 0777);
        $monolog->pushHandler($handler);

        $formatter = new LineFormatter(null, null, true, true);
        $handler->setFormatter($formatter);

        return $monolog;
    }

    /**
     * @param $name
     * @return Command|mixed
     */
    public function getLogger($name)
    {
        if (!isset(self::$instances[$name]) || !(self::$instances[$name] instanceof Logger)) {
            self::$instances[$name] = $this->getClient($name);
        }
        return self::$instances[$name];
    }
}