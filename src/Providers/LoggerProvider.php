<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 2018/9/6
 * Time: 下午5:11
 */

namespace Lin\Src\Providers;
use Illuminate\Support\ServiceProvider;
use Lin\Src\Support\Logger\Local;

class LoggerProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerLoggerDebug();
    }

    /**
     * 调试日志记录器
     */
    public function registerLoggerDebug()
    {

        $this->app->bind('logger_local', function () {
            return new Local();
        });
    }
}