<?php

class React_React_Block_Customer_Providers extends Mage_Core_Block_Template
{
	
	public function _construct()
	{
		$this->setTemplate('react/providers.phtml');
		
		$accounts = array();
		$services = Mage::getSingleton('react/services');
		
		foreach ($services->getProviders() as $provider)
			$accounts[$provider] = $services->isConnected($this->getCustomer(),$provider);
		
		$this->setAccounts($accounts);
		$this->setDeadlock(!$services->canRemoveProvider());
	
	}

	public function getCustomer()
	{
		return Mage::getSingleton('customer/session')->getCustomer();
	}

	public function getSession()
	{
		return Mage::getSingleton('core/session');	
	}
}