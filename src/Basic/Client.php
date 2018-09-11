<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 2018/9/11
 * Time: 下午2:05
 */

namespace Lin\Basic;

use Exception;

class Client
{
    /**
     * 响应结果
     * @param object $form
     * @param object $request
     * @param object $response
     * @param object $manager
     * @param $data array
     * @return string
     */
    public function run($form, $request, $response, $manager, $data = [])
    {
        $data = $form->validate($data);
        $paramter = [];
        try {
            $request->setData($data);
            $result = $manager->connect($request, $paramter);
        } catch (Exception $ex) {
            $result = app()['micro'];
            if (!$result) {
                $result = [
                    'errorCode' => 'E50000',
                    'errorMessage' => $ex->getMessage(),
                    'errorServer' => 'Systemt Error',
                ];
            }
            return $response->fail($data, $result);
        }
        return $response->callback($data, $result);
    }
}