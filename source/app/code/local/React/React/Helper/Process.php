<?php
class React_React_Helper_Process extends Mage_Core_Helper_Abstract
{
	const NO_EMAIL_VARIABLE = 'react_no_email';
	const SHARE_VARIABLE = 'react_share_info';
	const REDIRECT = 'react_redirect';
	const SESSION_REDIRECT = 'react_session_redirect';
	const WISHLIST_REDIRECT = 'react_wishlist_redirect';
	
	public function __construct()
	{
		Mage::register(self::REDIRECT, 'customer/account');
	}
	
	public function processRequest(array $result)
	{
		$customer = Mage::getModel('customer/customer');
		
		if ($result['applicationUserId'])
 		{
	 		$customer->load($result['applicationUserId']); 
		}
		else 
		{
			$customer->setData('website_id', Mage::app()->getStore()->getWebsiteId());
			$customer->loadByEmail($result['profile']['email']);
			if ($customer->getId())
			{
				#$this->getServices()->updateAccount($customer, $result);
				$this->getSession()->addError($this->__('This email is all ready in use. Please login before connecting a new social account.'));
				return true;
			}
			else 
			{
				$status = $this->_createCustomer($customer, $result);	
			 	if(!$status)
					return null;
			}		
		}
		
		Mage::getSingleton('customer/session')->setCustomerAsLoggedIn($customer);
		$this->getSession()->addSuccess($this->__('You have successfully logged in using your %s account.',$result['connectedWithProvider']));
		
		if ($this->getSession()->getData(self::SHARE_VARIABLE))
			$this->_redirect('react/share');
			
		return true;
	}
	
	public function canShare()
	{
		return $this->getServices()->isConnected($this->getSession()->getCustomer());
	}
	
	protected function _createCustomer(Mage_Customer_Model_Customer $customer, $result)
	{
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
				$this->getServices()->tokenSetUserId($id, $result['reactOAuthSession']);
			
			return true;
		}
		else
		{
			$this->_noMail($result);	
			return false;
		}
	}
	
	protected function _noMail(array $result = array())
	{
		
		$this->getSession()->setData(self::NO_EMAIL_VARIABLE, $result);
		#$this->getSession()->save();
		vd($this->getSession());
		$this->_redirect('react/index/email');	
	}
	
	public function getServices()
	{
		return Mage::getSingleton('react/services');
	}
	
	protected function getSession()
	{
		return Mage::getSingleton('core/session');
	}
	
	
	protected function _redirect($redirect = '')
	{
		Mage::unregister(self::REDIRECT);
		Mage::register(self::REDIRECT, (string)$redirect);
	}
	
	public function getRedirect()
	{
		$session_redirect = (string)$this->getSession()->getData(self::SESSION_REDIRECT,true);
		
		return (trim($session_redirect)) ? $session_redirect : Mage::registry(self::REDIRECT);
	}
	
	public function setRedirect($target = '')
	{
		$redirect = trim(str_replace(Mage::getBaseUrl(), '', $target),'/');
		$this->getSession()->setData(self::SESSION_REDIRECT, $redirect);
	}
}