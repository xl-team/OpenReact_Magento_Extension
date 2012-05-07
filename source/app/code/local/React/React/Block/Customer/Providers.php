<?php

class React_React_Block_Customer_Providers extends Mage_Core_Block_Template
{
	protected $_accounts = array();

	public function __construct()
	{
		$services = Mage::getSingleton('react/services');
		$connected = array_flip(array_intersect($services->getProviders(), $services->userGetProviders()));

		foreach ($services->getProviders() as $provider)
		{
			$this->_accounts[$provider] = array(
				'active' => isset($connected[$provider]),
				'provider' => $provider,
			);
		}
	}

	public function getAccounts()
	{
		return $this->_accounts;
	}
}