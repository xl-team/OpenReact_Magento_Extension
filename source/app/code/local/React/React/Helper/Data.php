<?php
class React_React_Helper_Data extends Mage_Core_Helper_Abstract
{
	const VAR_NOEMAIL = 'react_no_email';
	const VAR_SHARE = 'react_share_flag';
	const VAR_SESSION_CONNECTED_PROVIDERS = 'react_connected_providers';
	const VAR_SESSION_REDIRECT = 'react_session_redirect';
	const VAR_SESSION_CONFIRM_MAIL = 'react_session_confirm_mail';
	const VAR_AJAX_MODE = 'react_ajax';
	const CACHE_TAG_PROVIDERS = 'react_providers';
	const PROVIDERS_LIMIT = 1;
	
	/** (string) THe default redirect path 
	protected $_redirectPath = 'customer/account'; */
	/** (null) This hold XML-RPC client */
	protected $_client;
	protected $_canRemoveProvider = array();
	protected $_connectedProviders;
	protected $_isDialogBoxRendered = false;
	protected $_isAjax = false;
	/**
		The constructor function 
	*/
	public function __construct()
	{
		$this->_client = Mage::helper('react/client_magento');
		$this->_connectedProviders = $this->getSession()->getData(self::VAR_SESSION_CONNECTED_PROVIDERS);
		if(!is_array($this->_connectedProviders))
			$this->_connectedProviders = array();
		
		$this->_isAjax = (bool)$this->_getRequest()->getParam(self::VAR_AJAX_MODE, false);
	}
	
	/**
 		Login function 
	 	
 		Parameters: 
 			 provider - (string) The name of the provider you wish to login with.
 			 referer_url - (string) Url to redirect after login
 	*/
  	public function login($provider = '', $referer_url = null)
	{
		if(is_string($referer_url))
			$this->setSessionRedirect($referer_url); 
	
		return $this->_client->OAuthServer->tokenRequest($provider, $this->_getProcessUrl());
	}
	
	/**
		Fetch the current aplication providers
 	
		Returns:
			providers - (array) An array with all the available providers.
 	*/
	public function getProviders()
	{
		$providers = Mage::getSingleton('core/cache')->load(self::CACHE_TAG_PROVIDERS);
		if (!$providers)
		{
			$providers = serialize($this->_client->OAuthServer->getProviders());
			Mage::getSingleton('core/cache')->save($providers, self::CACHE_TAG_PROVIDERS, array(self::CACHE_TAG_PROVIDERS), 86400);
		}

		return unserialize($providers);	
	}
	
	/**
		Retrives a list of all providers of a customer.
		
		Parameters:
			customer - (Mage_Customer_Model_Customer) Customer moodel
			
		Returns:
			(array) An array containing the providers of the customer
	*/
	public function getConnectedAccounts(Mage_Customer_Model_Customer $customer)
	{
		if (!isset($this->_connectedProviders[$customer->getId()]))
		{
			$this->_connectedProviders[$customer->getId()] = array_intersect($this->getProviders(), Mage::helper('react/oauth')->userGetProviders($customer));
			$this->getSession()->setData(self::VAR_SESSION_CONNECTED_PROVIDERS, $this->_connectedProviders);
		}
		$this->_canRemoveProvider[$customer->getId()] = count($this->_connectedProviders[$customer->getId()]) > self::PROVIDERS_LIMIT;

		return $this->_connectedProviders[$customer->getId()];	
	}
	
	/**
		Checks if the customer can remove providers.
		
		Parameters:
			customer - (Mage_Customer_Model_Customer) Customer model
			
		Returns:
			(bool)
	*/
	public function canRemoveProvider(Mage_Customer_Model_Customer $customer)
	{
		if(!isset($this->_canRemoveProvider[$customer->getId()]))
			$this->getConnectedAccounts($customer);	
			
		return $this->_canRemoveProvider[$customer->getId()];	
	}
	
	/**
		Resets the the connected providers information stored in the session.
		
		Parameters:
			customer - (Mage_Customer_Model_Customer) Customer model 
		
	*/
	public function resetConnectedProviders(Mage_Customer_Model_Customer $customer)
	{
		$connected_providers = $this->getSession()->getData(self::VAR_SESSION_CONNECTED_PROVIDERS);
		unset($connected_providers[$customer->getId()]);
		$this->getSession()->setData(self::VAR_SESSION_CONNECTED_PROVIDERS, $connected_providers);		
	}
	
	/**
 		Helper function to determin if user is logged in
 		
 		Returns:
 			(boolean)
 	*/
	public function isLoggedIn()
	{
		return Mage::getSingleton('customer/session')->isLoggedIn();
	}
	
	/**
		Checkes if the user is connected to the specified provider
		
		Parameters:
			customer - (Mage_Customer_Model_Customer) Customer model
			provider - (string) The provider that need to be checked
	
		Returns: 
			(boolean) - If no provider is specified it will check if the customer is connected to any provider else it returnes if the customer is connected to the specified provider 
	*/
	public function isConnected(Mage_Customer_Model_Customer $customer, $provider = null)
	{
		if(!$this->isLoggedIn())
			return false;				
		if (is_null($provider))
			return (bool)count($this->getConnectedAccounts($customer));

		$connected = array_flip($this->getConnectedAccounts($customer));
		return isset($connected[$provider]);	
	}
	
	/**
 		Decodes the applcationUserId
 		
 		Parameters:
			applicationUserId - (string) Should be the applicationUserId returned by the client
			
		Returns: 
			(string) applicationUserId 	 
 	*/
	public function decodeApplicationUserId($applicationUserId = '')
	{
		return $applicationUserId;
	}
	
	/**
 		Encodes applcationUserId using the customer id and the local key
 		
 		Paramemeters:
 			customer - (Mage_Customer_Model_Customer) Customer model
		
		Returns:
			(string) applicationUserId to be used with the client
 	*/
	public function encodeApplicationUserId(Mage_Customer_Model_Customer $customer)
	{
		return $customer->getId();
	}
	/**
 		Gets the current redirect path
		
		Returns:
			(string)
	*/ /*
	
	public function getRedirectPath()
	{
		return $this->_redirectPath;
	} */
	
	/**
 		Generates the HTML for modal window.
 	
 		Returns:
 			(strign) The HTML and JS for the dialog box
 	*/
	public function getDialogBoxHtml()
	{
		if(!$this->_isDialogBoxRendered && !$this->isAjax());
		{
			$this->_isDialogBoxRendered = true;	
			return Mage::app()->getLayout()->createBlock('core/template')->setTemplate('react/dialog.phtml')->toHtml();
		}
		return '';
	}
	
	/**
		Helper function that returns the redirect url after login
		
		Parameters: 
			clear - (boolean) Clear the session variable? 
		
		Returns:
			(string) - Internerl path for _redirect function
	*/
	public function getSessionRedirect($clear = true)
	{
		return $this->getSession()->getData(self::VAR_SESSION_REDIRECT, (bool)$clear);
	}
	
	/**
 		Saves the redirect url to the session
 		
		Parameters: 
			url - (string) The URL 	
 	*/
	public function setSessionRedirect($url)
	{		
		$this->getSession()->setData(self::VAR_SESSION_REDIRECT, $url);
	}
	/**
		Clears the session variables
	*/
	public function clearSession()
	{
		$this->getSession()->unsetData(self::VAR_NOEMAIL);
		$this->getSession()->unsetData(self::VAR_SESSION_REDIRECT);	
		$this->getSession()->unsetData(self::VAR_SHARE);
		$this->getSession()->unsetData(self::VAR_SESSION_CONFIRM_MAIL);
	}
	
	/**
 		Function to set or return if AJAX or normal request.
 		
		Returns:
			isAjax - (bool) if is an AJAX request 
 	*/
 	public function isAjax()
	{
		return $this->_isAjax;
	}
	
	/**
	 	Helper function which retrives the current customer
 		
 		Returns:
 			(Mage_Customer_Model_Customer) Current Customer
 	*/
	public function getCustomer()
	{
		return Mage::helper('customer')->getCurrentCustomer();
	}
	
	/**
 		Helper function, returns the URL for tokenRequest method
 		
 		Returns:
 			(string)
	*/
	protected function _getProcessUrl()
	{
		return $this->_getUrl('react/index/process');
	}
	
	/**
 		Helper function that returns the session singleton. Used for messages.
 	
		Returns:
			(Mage_Core_Model_Session) Session Singleton 
 	*/
	public function getSession()
	{
		return Mage::getSingleton('core/session');
	}
}
?>