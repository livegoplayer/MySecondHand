<?php

namespace app\common\lib\sms\app\common\lib\email\Aliyun\Core;

class AcsResponse
{
	private $code;	
	private $message;
	
	public function getCode()
	{
		return $this->code;
	}
	
	public function setCode($code)
	{
		$this->code = $code;
	}
	
	public function getMessage()
	{
		return $this->message;
	}
	
	public function setMessage($message)
	{
		$this->message = $message;
	}
}