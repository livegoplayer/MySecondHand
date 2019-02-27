<?php
namespace app\api\lib\exception;

use think\Exception;

class APIException extends Exception
{
    /**
     * @var int
     */
    private $http_code;
    /**
     * @var int
     */
    private $status;
    /**
     * @var string
     */
    private $msg;
    /**
     * @var array
     */
    private $data_array;

    /**
     * APIException constructor.
     * @param string $message 业务提示信息
     * @param int $http_code http状态码
     * @param int $status 业务状态码
     * @param array $data_array 业务数据
     */
    public function __construct ($message = "", $http_code = 500, $status = 0, $data_array = [])
    {
        $this->http_code = $http_code;
        $this->status = $status;
        $this->msg = $message;
        $this->data_array = $data_array;
    }

    /**
     * @return int
     */
    public function getHttpCode ()
    {
        return $this->http_code;
    }

    /**
     * @param int $http_code
     */
    public function setHttpCode ($http_code)
    {
        $this->http_code = $http_code;
    }

    /**
     * @return int
     */
    public function getStatus ()
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus ($status)
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getMsg ()
    {
        return $this->msg;
    }

    /**
     * @param string $msg
     */
    public function setMsg ($msg)
    {
        $this->msg = $msg;
    }

    /**
     * @return array
     */
    public function getDataArray ()
    {
        return $this->data_array;
    }

    /**
     * @param array $data_array
     */
    public function setDataArray ($data_array)
    {
        $this->data_array = $data_array;
    }
}

