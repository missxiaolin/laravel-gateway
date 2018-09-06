<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 2018/9/6
 * Time: 下午6:51
 */

namespace Lin\Src\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Events\QueryExecuted;

/**
 * Class SqlQueryServiceProvider
 * @package Lin\Providers
 */
class SqlQueryServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $env = env('APP_ENV');
        $debug = env('SQL_DEBUG', false);
        if ($debug && in_array($env, ['local', 'prd'])) {
            \DB::listen(function (QueryExecuted $query) {
                $tmp = str_replace('?', '"' . '%s' . '"', $query->sql);
                $tmp = vsprintf($tmp, $query->bindings);
                $tmp = str_replace("\\", "", $tmp);
                $data = [
                    'name' => $query->connectionName,
                    'time' => $query->time,
                    'sql' => $tmp,
                ];
                $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                app('logger_sql')->debug($json);
            });
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}