<?php
class React_React_Model_Share_Message extends Varien_Object
{
	/**
		Internal Magento constructor
	*/
	public function _construct()
	{
		$data = array(
			'application_user_id' => '',
			'message' => '',
			'providers' => array(),
			'url' => '',
			'img' => '',
			'title' => '',
			'description' => '',
		);

		$this->addData($data);
	}
	
	/**
 		Sets the message parameters. 
 		
 		Returns:
 			(self)
 	*/
	public function init(array $args = array())
	{
		$_helper = Mage::helper('react');
		if (is_array($args))
			$data = array_intersect_key($args, $this->getData());

		if (empty($data['application_user_id']))
			$data['application_user_id'] = $_helper->encodeApplicationUserId($_helper->getCustomer());
		if (empty($data['providers']))
			$data['providers'] = array_values($_helper->getConnectedAccounts($_helper->getCustomer()));
		foreach ($data as $data_key => $data_value)
		{
			$key = str_replace(' ', '', ucwords(str_replace('_', ' ', $data_key)));
			$function = 'set'.$key;
			$this->$function($data_value);
		}
		
		return $this;
	}
	
	public function setDescription($description)
	{
		parent::setDescription($this->_trim($description));
	}

	public function setMessage($msg)
	{
		parent::setMessage($this->_trim($msg));
	}
	
	protected function _trim($string)
	{
		if(is_string($string) && strlen($string) > 255)
			$string = substr($string, 0, 252).'...';
		return $string;
	}
}