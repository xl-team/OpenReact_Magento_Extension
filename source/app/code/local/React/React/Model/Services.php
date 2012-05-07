<?php

class React_React_Model_Services extends Mage_Core_Model_Abstract
{
	protected $_eventPrefix = 'react_services';
	protected $_eventObject = 'react_services';

	public function __construct()
	{
		$this->setData('client',Mage::helper('react/client_magentoservices'));
	}

	public function getProviders()
	{
		$providers = Mage::getSingleton('core/cache')->load('react_provider_list');

		if (!$providers)
		{
			$providers = serialize($this->getClient()->OAuthServer->getProviders());
			Mage::getSingleton('core/cache')->save($providers, 'react_provider_list', array('react_magento_plugin', 86400));
		}

		return unserialize($providers);
	}

	public function tokenRequest($provider = null)
	{
		return $this->getClient()->OAuthServer->tokenRequest($provider, Mage::getUrl('react/index/process/'));
	}

	public function tokenAccess($params)
	{
		return $this->getClient()->OAuthServer->tokenAccess($params, true);
	}

	public function tokenSetUserId($id, $session)
	{
		$this->getClient()->OAuthServer->tokenSetUserId($id, $session);
	}

	public function userRemoveProvider($customer_id, $provider)
	{
		$this->getClient()->OAuthServer->userRemoveProvider($customer_id,$provider);
	}

	public function tokenUpdate(Mage_Customer_Model_Customer $customer, array $result)
	{
		if (isset($result['applicationUserId']))
			$this->userRemoveProvider($result['applicationUserId'], $result['connectedWithProvider']);

		$this->tokenSetUserId($customer->getId(),$result['reactOAuthSession']);
	}

	public function userGetProviders($customer_id = false)
	{
		if (!$customer_id)
			$customer_id = Mage::helper('customer')->getCustomer()->getId();

		$connected = $this->getClient()->OAuthServer->userGetProviders($customer_id);

		return $connected['connectedWithProviders'];
	}
}