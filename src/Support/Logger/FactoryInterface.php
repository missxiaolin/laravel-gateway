<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 2018/9/8
 * Time: 上午6:43
 */

namespace Lin\Support\Logger;

/**
 * Interface FactoryInterface
 * @package Lin\Support\Logger
 */
interface FactoryInterface
{
    /**
     * @param string $command redis command in lower case
     * @param $name
     * @return Command
     */
    public function getLogger($name);
}