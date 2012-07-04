<?php
class React_React_Model_Observer
{
	/**
 		Function called when a customer is deleted from the database.	
 	*/
	public function userRemoveProvider($event)
	{
		$_helper = Mage::helper('react/oauth');
		$customer = $event->getCustomer();

		foreach ($_helper->getConnectedAccounts($customer) as $provider)
			$_helper->userRemoveProvider($customer, $provider, true);
	}

	public function wishlistLoginRedirect($event)
	{
		$_helper = Mage::helper('react/process');
		$redirect = $_helper->getSession()->getData($_helper::WISHLIST_REDIRECT, true);
		if (Mage::helper('customer')->isLoggedIn() && is_array($redirect))
		{
			Mage::getSingleton('customer/session')->setBeforeWishlistUrl(Mage::getUrl($redirect['final']));
		}
		else
		{
			$params = $event->getControllerAction()->getRequest()->getParams();
			$redirect['wishlist'] = 'wishlist/index/add?';
			foreach ($params as $key => $param)
			{
				$redirect['wishlist'] .= $key . '=' . $param;
			}
			$redirect['final'] = $_helper->getRedirect();
		}
		$this->getSession()->setData($_helper::WISHLIST_REDIRECT, $redirect);
	}

	public function renewSession()
	{
		$_helper = Mage::helper('react');
		$_helper->getSession()->unsetData($_helper::VAR_NOEMAIL);
	}
}
?>