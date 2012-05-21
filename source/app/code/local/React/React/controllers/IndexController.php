<?php

class React_React_IndexController extends Mage_Core_Controller_Front_Action
{
	
	const NO_EMAIL_VARIABLE = 'react_no_email';
	
	public function indexAction()
	{
		$this->_redirect('customer/account');
	}

	public function loginAction()
	{
		
		$this->_redirect('customer/account');
		
		if($this->getSession()->isLoggedIn())
			return;
		
		$provider = $this->getRequest()->getParam('provider');
		
		$redirect_checkout = $this->getRequest()->getParam('checkout');
		$result =  Mage::getModel('react/services')->tokenRequest($provider);
		
		$this->getSession()->setData('react_checkout',$redirect_checkout);
		
		if (isset($result['redirectUrl']))
			$this->_redirectUrl($result['redirectUrl']);
	
	}

	public function processAction()
	{
		
		
		if($this->getSession()->getData('react_checkout')){
	 		$this->_redirect('checkout');
	 	}
	 	else 
	 	{
	 		$this->_redirect('customer/account');	
	 	}
	 	
	 	if($this->getSession()->isLoggedIn())
	 		return;
		
	 	$this->getSession()->unsetData('react_checkout');
		
		$service = Mage::getSingleton('react/services');
		$result = $service->tokenAccess($this->getRequest()->getParams());

  		$customer = Mage::getModel('customer/customer');
 		
		if($result['applicationUserId'])
 		{
	 		$customer->load($result['applicationUserId']); 
		}
		else 
		{
			$customer->setData('website_id', Mage::app()->getStore()->getWebsiteId());
			$customer->loadByEmail($result['profile']['email']);
			
			if($customer->getId())
			{
				$service->updateAccount($customer, $result);
			}
			else 
			{
				$status = $this->_createCustomer($customer, $result);	
				if(!$status)
					return;
			}		
		}
		
		$this->getSession()->setCustomerAsLoggedIn($customer);
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
		
				return true;
		}
		else
		{
			$this->_noMail($result);	
			return false;
		}
	}
	
	public function emailAction()
	{
		
		if(!$this->getSession()->getData(self::NO_EMAIL_VARIABLE))
		{
		
			$this->_redirect('customer/account');
			return;
		}				
		
		$this->loadLayout();
		$this->_initLayoutMessages('customer/session');
		$this->_initLayoutMessages('catalog/session');

		$this->renderLayout();
	}
	
	public function emailPostAction()
	{
		$this->_redirect('customer/account');
		
		$result = $this->getSession()->getData(self::NO_EMAIL_VARIABLE, true);
		if(!$result)		
			return;
		
		$result['profile']['email'] = $this->getRequest()->getPost('email');
		
		$service = Mage::getSingleton('react/services');
		
		$customer = Mage::getModel('customer/customer');
		$customer->setData('website_id', Mage::app()->getStore()->getWebsiteId());
		$customer->loadByEmail($result['profile']['email']);
		
		if($customer->getId()) 
		{
			$service->updateAccount($customer, $result);
		}
		else 
		{
			$status = $this->_createCustomer($customer, $result);	
			if(!$status)
			{
				$this->getSession()->addError($this->__('We are sorry you can not conect using %s',$result['connectedWithProvider']));	
				$this->getSession()->unsetData(self::NO_EMAIL_VARIABLE);
				return;
			}
		}
		
		$this->getSession()->setCustomerAsLoggedIn($customer);
		
		
	}
	
	protected function _noMail(array $result = array())
	{
		$this->getSession()->setData(self::NO_EMAIL_VARIABLE,$result);
		$this->_redirect('react/index/email');	
	}
	
	public function getSession()
	{
		return Mage::getSingleton('customer/session');
	}
}
?>