<?php

class React_React_Exception extends Mage_Core_Exception
{
	public function __construct($message, array $params = array(), $cause = null, $code = 0)
	{
		if (false !== strpos($message, '%s') && is_array($params) && !empty($params))
			$message = vsprintf($message, $params);

		$this->setMessage($message);
	}
}