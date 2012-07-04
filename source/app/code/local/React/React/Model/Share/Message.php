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
		$this->addData($data);
		return $this;
	}
}