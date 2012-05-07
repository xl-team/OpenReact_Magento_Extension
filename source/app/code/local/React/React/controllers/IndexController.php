<?php

class React_React_IndexController extends Mage_Core_Controller_Front_Action
{
	public function indexAction()
	{
		#$this->_redirect('*/login');
	}

	public function loginAction()
	{
		$provider = $this->getRequest()->getParam('provider');
		$result =  Mage::getModel('react/services')->tokenRequest($provider);

		if (isset($result['redirectUrl']))
			$this->_redirectUrl($result['redirectUrl']);
	}

	public function processAction()
	{
		$service = Mage::getSingleton('react/services');
		$result = $service->tokenAccess($this->getRequest()->getParams());
		$customer = Mage::getModel('customer/customer')->load($result['applicationUserId']);
		$this->_redirect('customer/account');

		if (!$customer->getId())
		{
			if (Mage::helper('customer')->isLoggedIn())
			{
				$current_customer = Mage::getSingleton('customer/session')->getCustomer();
				$service->tokenSetUserId($current_customer->getId(), $result['reactOAuthSession']);
			}
			else
			{
				$test_customer = Mage::getModel('customer/customer')->setData('website_id', Mage::app()->getStore()->getWebsiteId());
				$test_customer->loadByEmail($result['profile']['email']);

				if ($test_customer->getId())
				{
					$service->tokenUpdate($test_customer, $result);
					$customer = $test_customer;
				}
				else
				{
					$this->_createCustomer($customer, $result);
				}
			}
		}

		Mage::getSingleton('customer/session')->setCustomer($customer);
	}

	protected function _createCustomer(Mage_Customer_Model_Customer $customer, $result){

		$profile = $result['profile'];
		$service =  Mage::getSingleton('react/services');

		if ($profile['email'])
		{
				$customer->setEmail($profile['email']);
				$customer->setPassword(Mage::helper('core')->getRandomString(6));
				$name = explode(' ', $profile['real_name'],2);
				$customer->setFirstname($name[0]);

				if (isset($name[1]))
					$customer->setLastname($name[1]);

				$customer->setDob($profile['birthday']);

				switch($profile['gender'])
				{
					case 'male':
						$customer->setGender('1');
					break;

					case 'female':
						$customer->setGender('2');
					break;
				}

				$customer->save();

				if ($id = $customer->getId())
					$service->tokenSetUserId($id, $result['reactOAuthSession']);
		}
		else
		{
			#redirect to register + prefill form.
		}
	}
}
?>