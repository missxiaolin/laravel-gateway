<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 2018/9/10
 * Time: 上午11:50
 */

namespace Lin\Basic;

use Exception;
use GuzzleHttp\Client as GuzzleClient;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Client
 * @package Lin\Basic
 */
abstract class Client
{
    protected $guzzleClient;

    protected $baseUri;

    protected $timeout = 2;

    /**
     * @return GuzzleClient
     * @throws Exception
     */
    protected function getGuzzleClient()
    {
        if (isset($this->guzzleClient) && $this->guzzleClient instanceof GuzzleClient) {
            return $this->guzzleClient;
        }
        return $this->guzzleClient = new GuzzleClient([
            'base_uri' => $this->getBaseUri(),
            'timeout' => $this->getTimeout(),
        ]);
    }

    /**
     * @return mixed
     * @throws Exception
     */
    protected function getBaseUri()
    {
        if (!isset($this->baseUri)) {
            throw new Exception('Please rewrite getBaseUri function or baseUri property');
        }
        return $this->baseUri;
    }

    /**
     * @return int
     */
    protected function getTimeout()
    {
        return $this->timeout;
    }

    protected function beforeExecute($method, $arguments)
    {
    }

    protected function afterExecute($method, $arguments, ResponseInterface $response)
    {
    }

    /**
     * @param $method
     * @param $arguments
     * @return mixed
     */
    protected function handleRequest($method, $arguments)
    {
        return $arguments;
    }

    /**
     * @param $method
     * @param $arguments
     * @param $response
     * @return mixed
     */
    protected function handleResponse($method, $arguments, $response)
    {
        return json_decode($response, true);
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws Exception
     */
    public function __call($name, $arguments)
    {
        $arguments = $this->handleRequest($name, $arguments);
        $this->beforeExecute($name, $arguments);

        // 接口调用
        /** @var ResponseInterface $result */
        $result = $this->getGuzzleClient()->$name(...$arguments);

        $this->afterExecute($name, $arguments, $result);
        $response = $this->handleResponse($name, $arguments, $result->getBody());
        return $result;
    }
}