<?php

class React_React_Block_Login extends Mage_Core_Block_Template 
{
	
	
	protected function _construct()
	{
		$this->setTemplate('react/login.phtml');
		
		$this->setTitle($this->__('Login or register with your social network'));
		$this->setFieldset(false);
		$this->setHeadingTag('h1');		
		$this->_isCheckout();
		$this->setShowButton(true);
				
		parent::_construct();	
	
		
	}
	
	protected function _isCheckout()
	{
 		$isCheckout = $this->getRequest()->getModuleName() == 'checkout';
 		$this->setIsCheckout($isCheckout);
	}
	
	public function isCheckout()
	{
		return $this->getIsCheckout();
	}
	
	public function getProviders()
	{
		return Mage::getSingleton('react/services')->getProviders();	
	}

	public function getLoginUrl($provder = null)
	{
		return $this->getBaseUrl().'react/index/login/checkout/'.$this->isCheckout().'/provider/';
	}
	
	public function getCssClass()
	{
		return ($this->getFieldset()) ? 'fieldset' : 'content' ;
	}
	
	public function getDescription()
	{
		if (!isset($this->_data['description']))
			return Mage::getStoreConfig('react/settings/description');
		
		return $this->_data['description'];
	}
 	
 	public function showButton()
 	{
		return (bool) $this->getShowButton(); 	
 	}
 	
 	public function getSession()
 	{
 		return Mage::getSingleton('customer/session');
 	}
  
}

?>