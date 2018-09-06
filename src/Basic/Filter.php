<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 2018/9/6
 * Time: 下午4:46
 */

namespace Lin\Src\Basic;

/**
 * Class Filter
 * @package Lin\Src\Basic
 */
class Filter
{
    /**
     *
     * @var type
     */
    protected $data = [];

    /**
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->request = request();
        $this->data = $this->request->all();
        $this->build();
        $this->removeExpand();
    }

    /**
     * 获取数据
     * @return type
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * 构建
     */
    protected function build()
    {

    }

    /**
     * 移除多余参数
     */
    protected function removeExpand()
    {
        unset($this->data['expand']);
    }
}