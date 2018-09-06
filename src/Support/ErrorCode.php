<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 2018/9/6
 * Time: 下午5:01
 */

namespace Lin\Src\Support;

use xiaolin\Enum\Enum;

class ErrorCode extends Enum
{
    /**
     * @Message('参数错误')
     */
    public static $ENUM_SYSTEM_API_Form_ERROR = 300;
}