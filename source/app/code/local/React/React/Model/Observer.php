<?php

class React_React_Model_Observer
{
	public function userRemoveProvider($event)
	{
		$service = Mage::getSingleton('react/services');
		$customer_id = $event->getCustomer()->getId();

		foreach($service->getProviders() as $provider)
			$service->userRemoveProvider($customer_id,$provider);
	}
}
?>