<?php
class React_React_Model_Observer
{
	const NO_EMAIL_VARIABLE = 'react_no_email';


	public function userRemoveProvider($event)
	{
		$service = Mage::getSingleton('react/services');
		$customer_id = $event->getCustomer()->getId();

		foreach ($service->getConnectedAccounts($customer_id) as $provider)
			$service->userRemoveProvider($customer_id, $provider, true);
	}

	public function wishlistLoginRedirect($event)
	{
		$_helper = Mage::helper('react/process');
		$redirect = $this->getSession()->getData($_helper::WISHLIST_REDIRECT, true);
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
		$this->getSession()->unsetData(self::NO_EMAIL_VARIABLE);
	}

	protected function getSession()
	{
		return Mage::getSingleton('core/session');
	}
}
?>