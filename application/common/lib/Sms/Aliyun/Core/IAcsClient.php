<?php

namespace app\common\lib\sms\Aliyun\Core;
interface IAcsClient
{
	public function doAction($requst);
}