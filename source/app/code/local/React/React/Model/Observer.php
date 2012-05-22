<?php
class React_React_Model_Observer
{
	const NO_EMAIL_VARIABLE = 'react_no_email';  
	
	public function userRemoveProvider($event)
	{
		$service = Mage::getSingleton('react/services');
		$customer_id = $event->getCustomer()->getId();
		
		foreach($service->getConnectedAccounts($customer_id) as $provider)
			$service->userRemoveProvider($customer_id, $provider, true);
	}

	public function renewSession()
	{
		$this->getSession()->unsetData(self::NO_EMAIL_VARIABLE);
	}

	protected function getSession()
	{
		return Mage::getSingleton('customer/session');
	}

}
?>