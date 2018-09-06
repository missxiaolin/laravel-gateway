<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 2018/9/6
 * Time: 下午4:49
 */

namespace Lin\Src\Basic;

use Illuminate\Contracts\Support\Arrayable;
use Lin\Src\Exceptions\CodeException;
use Illuminate\Support\MessageBag;
use Lin\Src\Support\ErrorCode;
use Carbon\Carbon;
use Validator;


abstract class Form implements Arrayable
{
    /**
     * The message bag instance.
     *
     * @var \Illuminate\Support\MessageBag
     */
    private $messages;

    /**
     * The data under validation.
     *
     * @var array
     */
    protected $data = [];

    /**
     * The old data.
     *
     * @var array
     */
    private $_old_data = [];


    /**
     * Construct and init MessageBag.
     */
    public function __construct()
    {
        $this->messages = new MessageBag();
    }

    /**
     * @param $data
     * @return array
     * @throws CodeException
     * @throws \ReflectionException
     * @throws \xiaolin\Enum\Exception\EnumException
     */
    public function validate($data)
    {
        $this->_old_data = $this->data = $data;
        $this->init();
        $validator = Validator::make($this->data, $this->rules(), $this->messages(), $this->attributes());
        $validator = $this->initValidator($validator);
        if ($validator->fails()) {
            $this->failedValidation($validator->messages());
        }
        $this->validation();

        if (!$this->isValid()) {
            $this->failedValidation($this->messages);
        }
        request()->merge(['form' => $this]);

        return $this->data;
    }

    public function getData()
    {
        return $this->data;
    }

    /**
     * init
     */
    protected function init()
    {
    }

    /*
     * initValidator
     */
    protected function initValidator($validator = null)
    {
        return $validator;
    }

    /**
     * Get the validation rules.
     *
     * @return array
     */
    abstract protected function rules();

    /**
     * validate data by value object;
     * and transform data to valueObject
     *
     * @return mixed
     */
    abstract protected function validation();

    /**
     * 验证是否通过
     *
     * @return bool
     */
    public function isValid()
    {
        return $this->messages->count() === 0;
    }


    /**
     * 时间
     *
     * @param $attribute
     * @param $value
     *
     * @return \Carbon\Carbon
     */
    protected function validateDate($attribute, $value)
    {
        if ($value instanceof Carbon) {
            return $value;
        }
        if (strtotime($value) === false) {
            $this->addError($attribute, '时间格式不正确');
        } else {
            return new Carbon($value);
        }
        return Carbon::now();
    }

    /**
     * 指定格式的时间
     *
     * @param        $attribute
     * @param        $value
     * @param string $format
     *
     * @return \Carbon\Carbon
     */
    protected function validateDateFormat($attribute, $value, $format = 'Y-m-d')
    {
        $parsed = date_parse_from_format($format, $value);
        if ($parsed['error_count'] === 0 && $parsed['warning_count'] === 0) {
            return new Carbon($value);
        }
        $this->addError($attribute, '时间格式不正确');

        return new Carbon();
    }


    /**
     * @param MessageBag $message
     * @throws CodeException
     * @throws \ReflectionException
     * @throws \xiaolin\Enum\Exception\EnumException
     */
    protected function failedValidation(MessageBag $message)
    {
        $this->log($message);
        throw new CodeException(ErrorCode::$ENUM_SYSTEM_API_Form_ERROR, $message->first());
    }

    /**
     * 记录日志
     * @param $message
     */
    public function log($message)
    {
        $error = [
            'url' => request()->url(),
            'method' => request()->getMethod(),
            'data' => $this->data,
            'error' => $message,
        ];
        logger_instance('validator-error', $error, request()->all());
    }

    /**
     * Format the errors from the given Validator instance.
     *
     *
     * @param \Illuminate\Support\MessageBag $message
     *
     * @return array
     */
    protected function formatErrors(MessageBag $message)
    {
        return array_get(current($message->getMessages()), 0);
    }

    /**
     * Set custom messages for validator errors.
     *
     * @return array
     */
    protected function messages()
    {
        return [];
    }

    /**
     * Set custom attributes for validator errors.
     *
     * @return array
     */
    protected function attributes()
    {
        return [];
    }

    /**
     * Add an error message to the validator's collection of messages.
     *
     * @param  string $attribute
     * @param  string $message
     *
     * @return void
     */
    protected function addError($attribute, $message)
    {
        if ($message) {
            $this->messages->add($attribute, $this->doReplacements($message, $attribute));
        }
    }

    /**
     * Replace all error message place-holders with actual values.
     *
     * @param  string $message
     * @param  string $attribute
     *
     * @return string
     */
    protected function doReplacements($message, $attribute)
    {
        $attribute = preg_replace("/\.\d\./", '.', $attribute);

        return array_get($this->attributes(), $attribute) . $message;
    }

    protected $attributes = [];

    /**
     * Dynamically retrieve attributes on the model.
     *
     * @param  string $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        return array_get($this->attributes, $key);
    }

    /**
     * Dynamically set attributes on the model.
     *
     * @param  string $key
     * @param  mixed $value
     *
     * @return mixed
     */
    public function __set($key, $value)
    {
        return array_set($this->attributes, $key, $value);
    }

    public function toArray()
    {
        return $this->attributes;
    }


    protected function formatDataFromHorToVert(array $data, array $keys = [])
    {

        $result = [];
        $first_data = current($data);
        if (!is_array($first_data)) return $result;
        foreach ($first_data as $key_num => $first_value) {
            $tmp = [];
            $is_null = true;
            foreach ($data as $key_name => $values) {
                if (in_array($key_name, $keys) || empty($keys)) {
                    $value = array_get($values, $key_num);
                    $tmp[$key_name] = $value;
                    if (!empty($value)) {
                        $is_null = false;
                    }
                }
            }
            if (!$is_null) {
                $result[] = $tmp;
            }
        }

        return $result;
    }

    /**
     * @param $key
     * @param $value
     *
     * @return array
     */
    protected function formatArray($key, $value)
    {
        return compact('key', 'value');
    }

    /**
     * @return string
     */
    protected function implodeAll()
    {
        return implode('.', func_get_args());
    }
}