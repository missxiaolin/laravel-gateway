<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 2018/9/6
 * Time: 下午5:59
 */

namespace Lin\Src\Basic;

use GuzzleHttp\Client;
use Exception;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\BadResponseException;

/**
 * Class Manager
 * @package Lin\Src\Basic
 */
class Manager
{
    protected $errorMessage = '网络错误，请稍后再试';

    /**
     *
     * @var \App\Micro\Basic\Request
     */
    protected $request;


    /**
     *
     * @return \App\Micro\Basic\Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * 连接后端请求
     * @param $request object
     * @param array $paramter
     * @return mixed
     * @throws Exception
     */
    public function connect($request, $paramter = [])
    {

        $this->request = $request;

        $time = $this->request->getTimeOut();

        $client = new Client([
            'base_uri' => $this->getHost(),
            'timeout' => $time,
        ]);

        $data = $this->request->getData();

        $post = [
            'front' => request()->url(),
            'backend' => $this->getHost() . $this->request->getUri(),
            'method' => $request->getMethod(),
            'start' => $this->getTimeNow(),
            'end' => null,
            'request' => $data,
        ];

        $options = [
            'json' => $data,
        ];

        $response = [];
        try {

            if ($this->request->getMethod() == 'GET') {
                $response = $client->get($this->request->getUri(), $options);
            }
            if ($this->request->getMethod() == 'POST') {
                $response = $client->post($this->request->getUri(), $options);
            }

            if ($this->request->getMethod() == 'PATCH') {
                $response = $client->patch($this->request->getUri(), $options);
            }

            if ($this->request->getMethod() == 'DELETE') {
                $response = $client->delete($this->request->getUri(), $options);
            }

        } catch (ConnectException $ce) {

            $post['error'] = [
                'errorCode' => $ce->getCode(),
                'errorMessage' => $ce->getMessage(),
                'errorFile' => $ce->getFile(),
                'errorLine' => $ce->getLine(),
            ];
            $post['end'] = $this->getTimeNow();

            app()['micro'] = [
                'errorCode' => 'E50101',
                'errorMessage' => '服务超时',
                'errorServer' => $request->getName(),
            ];

            $this->doLogger($this->request->getConnectLogName(), $post);

            $this->stopExecution($this->errorMessage, 500);
        } catch (BadResponseException $be) {

            $post['error'] = [
                'errorCode' => $be->getCode(),
                'errorMessage' => $be->getMessage(),
                'errorFile' => $be->getFile(),
                'errorLine' => $be->getLine(),
            ];
            $post['prepare']['end'] = $this->getTimeNow();

            app()['micro'] = [
                'errorCode' => 'E50202',
                'errorMessage' => '服务异常',
                'errorServer' => $this->request->getName(),
            ];
            $this->doLogger($this->request->getBadLogName(), $post);

            $this->stopExecution($this->errorMessage, 500);
        } catch (ServerException $se) {

            $post['error'] = [
                'errorCode' => $se->getCode(),
                'errorMessage' => $se->getMessage(),
                'errorFile' => $se->getFile(),
                'errorLine' => $se->getLine(),
            ];
            $post['prepare']['end'] = $this->getTimeNow();

            app()['micro'] = [
                'errorCode' => 'E50303',
                'errorMessage' => '服务异常',
                'errorServer' => $this->request->getName(),
            ];
            $this->doLogger($this->request->getServerLogName(), $post);

            $this->stopExecution($this->errorMessage, 500);

        } catch (Exception $ex) {
            $post['error'] = [
                'errorCode' => $ex->getCode(),
                'errorMessage' => $ex->getMessage(),
                'errorFile' => $ex->getFile(),
                'errorLine' => $ex->getLine(),
            ];
            $post['end'] = $this->getTimeNow();

            app()['micro'] = [
                'errorCode' => 'E50404',
                'errorMessage' => '请求异常',
                'errorServer' => $this->request->getName(),
            ];

            logger_instance($this->request->getResultFailLogName(), $post);

            throw new Exception($this->errorMessage, 500);
        }

        $body = json_decode($response->getBody(), true);

        $post['end'] = $this->getTimeNow();
        $post['response'] = $body;

        $this->doLogger($this->request->getResultSucessLogName(), $post);
        
        return $body;
    }

    /**
     * 获取API接口
     * @throws Exception
     */
    protected function getHost()
    {
        throw new Exception('Method getHost Error');
    }

    /**
     * @return \Illuminate\Config\Repository|mixed
     * @throws Exception
     */
    protected function getConfig()
    {
        $env = env('APP_ENV', null);
        $file = 'backend.' . $env . '.request';
        $config = config($file);
        if (!$config) {
            throw new Exception($config . ' 配置未找到');
        }
        return $config;
    }

    /**
     * 请求时间精确到毫秒级
     * @return string
     */
    protected function getTimeNow()
    {
        $micro = explode('.', microtime(true));
        $time = sprintf("%04d", array_get($micro, 1, 0));
        $time = date('Y-m-d H:i:s.') . $time;
        return $time;
    }

    /**
     * 日志记录
     * @param $name
     * @param $post
     */
    protected function doLogger($name, $post)
    {
        logger_instance($name, $post);
    }

    /**
     * 停止执行
     * @param type $message
     * @param type $code
     * @throws Exception
     */
    protected function stopExecution($message, $code = 500)
    {
        throw new Exception($message, $code);
    }
}