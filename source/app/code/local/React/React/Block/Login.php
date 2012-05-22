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
		
		#$this->_noEmail();
				
		parent::_construct();	
	
		
	}		
	
	/** protected function _noEmail()
	{
		$no_provider = $this->getRequest()->getParam('react') == 'no_provider';	
		$provider_info = $this->getSession()->getData('react_no_email',true);
		
		
		if(!is_array($provider_info))
			return;
		
		$name = explode(' ',$provider_info['real_name'],2);
		$provider_info['first_name'] = $name[0];
		$provider_info['last_name'] = $name[1];
		$this->setProviderInfo($provider_info);
	} */
	
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
		return Mage::getStoreConfig('react/settings/description');
	}
 	
 	
 	public function getSession()
 	{
 		return Mage::getSingleton('customer/session');
 	}
  
}

?>