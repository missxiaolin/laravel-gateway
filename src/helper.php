<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 2018/9/6
 * Time: 下午3:56
 */

if (!function_exists('logger_instance')) {

    /**
     * 异步写日志
     *
     * @param  string $name
     * @param  string $message
     * @param  array $context
     * @return \Illuminate\Contracts\Logging\Log|null
     */
    function logger_instance($name, $message = null, $context = [])
    {
        $logger = [
            'body' => $message,
            'context' => $context,
        ];

        logger_local($name, json_encode_ori($logger));
    }
}

if (!function_exists('json_encode_ori')) {

    /**
     * JSON
     *
     * @param  string $name
     * @param  string $message
     * @return \Illuminate\Contracts\Logging\Log|null
     */
    function json_encode_ori($message)
    {
        if (env('APP_ENV') == 'production') {
            return json_encode($message, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }
        return json_encode($message, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

}

if (!function_exists('logger_local')) {
    /**
     * Log a debug message to the logs.
     *
     * @param  string $name
     * @param  string $message
     * @param  array $context
     * @return \Illuminate\Contracts\Logging\Log|null
     */
    function logger_local($name, $message = null, array $context = [])
    {

        if (is_null($message)) {
            return app('logger_local')->getLogger($name);
        }

        return app('logger_local')->getLogger($name)->debug($message, $context);
    }
}