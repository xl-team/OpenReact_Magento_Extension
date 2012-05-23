<?php

class React_React_Model_Services extends Varien_Object
{
	const PROVIDERS_LIMIT = 1;
	const SESSION_VARIABLE = 'react_connected_providers';
	const CACHE_VARIABLE = 'react_provider_list';
	protected $_eventPrefix = 'react_services';
	protected $_eventObject = 'react_services';
	protected $_canRemoveProvider = null;
	protected $_redirectUrl;
	protected $_connectedProviders;
	
	public function __construct()
	{
		parent::__construct();
		$this->setClient(Mage::helper('react/client_magentoservices'));
		$redirect_url = 'react/'.Mage::app()->getRequest()->getControllerName().'/process';
		$this->_redirectUrl = Mage::getUrl($redirect_url);
		
		$this->_connectedProviders = $this->getSession()->getData(self::SESSION_VARIABLE);	
		
	}

	public function getProviders()
	{
		$providers = Mage::getSingleton('core/cache')->load(self::CACHE_VARIABLE);

		if (!$providers)
		{
			$providers = serialize($this->getClient()->OAuthServer->getProviders());
			Mage::getSingleton('core/cache')->save($providers, self::CACHE_VARIABLE, array('react_magento_plugin', 86400));
		}

		return unserialize($providers);
	}

	public function tokenRequest($provider = null)
	{
		return $this->getClient()->OAuthServer->tokenRequest($provider, $this->_redirectUrl);
	}

	public function tokenAccess($params)
	{
		return $this->getClient()->OAuthServer->tokenAccess($params, true);
	}

	public function tokenSetUserId($id, $session)
	{
		$this->getClient()->OAuthServer->tokenSetUserId($id, $session);
	}

	public function userRemoveProvider($customer_id, $provider, $event = false)
	{
		$flag = ($event) ? true : $this->canRemoveProvider();
		if ($flag)
			$this->getClient()->OAuthServer->userRemoveProvider($customer_id, $provider);
	}

	public function updateAccount(Mage_Customer_Model_Customer $customer, array $result)
	{
		if (isset($result['applicationUserId']) && !$this->isConnected($customer, $result['connectedWithProvider']))
		{
			$this->tokenSetUserId($customer->getId(), $result['reactOAuthSession']);
			$this->resetConnectedProviders();
		}
	}

	public function userGetProviders($customer_id = false)
	{
		if (!$customer_id)
			$customer_id = $this->getCustomer()->getId();

		$connected = $this->getClient()->OAuthServer->userGetProviders($customer_id);

		return $connected['connectedWithProviders'];
	}

	public function getConnectedAccounts($customer_id = false) 
	{
 		if(is_null($this->_connectedProviders))
 		{
			$this->_connectedProviders = array_intersect($this->getProviders(), $this->userGetProviders($customer_id));
			$this->getSession()->addData('react_connected_providers', $this->_connectedProviders);
		}
		
		$this->_canRemoveProvider = count($this->_connectedProviders) > self::PROVIDERS_LIMIT;
					
		return $this->_connectedProviders;
	}
	
	public function canRemoveProvider()
	{
		if(is_null($this->_canRemoveProvider))
			$this->getConnectedAccounts();	
	
		return $this->_canRemoveProvider;
	}
	
	public function getSession()
	{
		return Mage::getSingleton('customer/session');
	}
	
	public function getCustomer()
	{
		return $this->getSession()->getCustomer();
	}
	
	public function resetConnectedProviders()
	{
		$this->_connectedProviders = null;
		$this->getSession()->unsetData(self::SESSION_VARIABLE);	
	}
	
	public function isConnected(Mage_Customer_Model_Customer $customer, $provider = null)
	{	
		
		if(is_null($provider))
			return (bool)count($this->getConnectedAccounts());
		
		$connected = array_flip($this->getConnectedAccounts());
		return isset($connected[$provider]);
	}	
}