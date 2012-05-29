<?php

class React_React_Block_Login extends Mage_Core_Block_Template 
{
	
	
	protected function _construct()
	{
		$helper = Mage::helper('react/process');
		$this->setTemplate('react/login.phtml');
		
		$this->setTitle($this->__('Login or register with your social network'));
		$this->setFieldset(false);
		$this->setHeadingTag('h1');		
		$this->setShowButton(true);
		
		$helper->setRedirect($this->_getRefererUrl());
				
		parent::_construct();
	}
	
	protected function _isCheckout()
	{
		$request_uri = $this->getRequest()->getServer('REQUEST_URI');
		return strpos($request_uri, 'checkout') || $this->getRequest()->getModuleName() == 'checkout';
	}
	
	public function getProviders()
	{
		return Mage::getSingleton('react/services')->getProviders();	
	}

	public function getLoginUrl($provder = null)
	{
		return $this->getBaseUrl().'react/index/login/provider/';
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
 		return Mage::getSingleton('core/session');
 	}
  
	protected function _getRefererUrl()
	{
		$helper = Mage::helper('react/process');
		
		if ($this->_isCheckout())
		{
			$model = $this->getRequest()->getModuleName();
			$controller = $this->getRequest()->getControllerName();
        	$action = $this->getRequest()->getActionName();
			
			return $model.'/'.$controller.'/'.$action;
		}
		if ($refererUrl = $this->getSession()->getData($helper::WISHLIST_REDIRECT));
		{
			if(is_array($refererUrl)){
				return $refererUrl['wishlist'];
			} else {
				$this->getSession()->unsetData($helper::WISHLIST_REDIRECT);
				return $refererUrl;	
			}
					
		}				
		
		$refererUrl = $this->getRequest()->getServer('HTTP_REFERER');
        if ($url = $this->getRequest()->getParam('referer_url')) 
            $refererUrl = $url;
        if ($url = $this->getRequest()->getParam('r64')) 
            $refererUrl = Mage::helper('core')->urlDecode($url);
        if ($url = $this->getRequest()->getParam('uenc')) 
            $refererUrl = Mage::helper('core')->urlDecode($url);

        $refererUrl = Mage::helper('core')->escapeUrl($refererUrl);

        if (!$this->_isUrlInternal($refererUrl)) 
            $refererUrl = Mage::app()->getStore()->getBaseUrl();
        
		return $refererUrl;
	}
	
	protected function _isUrlInternal($url)
	{
		if (strpos($url, 'http') !== false) 
		{
            if ((strpos($url, Mage::app()->getStore()->getBaseUrl()) === 0)
                || (strpos($url, Mage::app()->getStore()->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK, true)) === 0)
            ) {
                return true;
            }
        }
        return false;
	}

}

?>