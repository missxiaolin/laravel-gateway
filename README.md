# laravel-gateway

安装

~~~
composer require xiaolin/laravel-gateway
~~~

注册日志

config/app.php

~~~
'providers'=> [
    // 日志
    Lin\Src\Providers\LoggerProvider::class,
]
~~~

form 使用

~~~
class RouteAddForm extends Form
{
    public function rules()
    {
        return [
            'name' => 'required',
            'code' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'required' => ':attribute必填。',
        ];
    }

    public function attributes()
    {
        return [
            'name' => '规则名称',
            'code' => '编码',
        ];
    }

    public function validation()
    {
    }
}
~~~