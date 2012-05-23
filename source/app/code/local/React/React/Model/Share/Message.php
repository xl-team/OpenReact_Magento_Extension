<?php
class React_React_Model_Share_Message extends Varien_Object
{
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

	public function init(array $args = array())
	{
		if (is_array($args))
			$data = array_intersect_key($args,$this->getData());	
			
		if (empty($data['application_user_id']))
			$data['application_user_id'] = $this->getCustomer()->getId();
		
		$this->addData($data);
		
		return $this;
	}
	
	public function getCustomer()
	{
		return Mage::getSingleton('customer/session')->getCustomer();
	}
	
}
