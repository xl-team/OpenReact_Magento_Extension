<?php
class React_React_Block_Customer_Providers extends Mage_Core_Block_Template
{
	/**
 		Magento internal constructor
 	*/
	public function _construct()
	{
		$this->setTemplate('react/customer/providers.phtml');
		$_helper = Mage::helper('react');
		$accounts = array();

		foreach ($_helper->getProviders() as $provider)
			$accounts[$provider] = $_helper->isConnected($_helper->getCustomer(), $provider);
		$this->setAccounts($accounts);
		$this->setDeadlock(!$_helper->canRemoveProvider($_helper->getCustomer()));
	}
}