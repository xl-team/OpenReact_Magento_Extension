<?php
class React_React_Helper_Process extends Mage_Core_Helper_Abstract
{
	const NO_EMAIL_VARIABLE = 'react_no_email';
	const SHARE_VARIABLE = 'react_share_info';
	public $_redirect = null;
	
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
				$this->getServices()->updateAccount($customer, $result);
			}
			else 
			{
				$status = $this->_createCustomer($customer, $result);	
				if(!$status)
					return false;
			}		
		}
		
		$this->getSession()->setCustomerAsLoggedIn($customer);
		
		if ($this->getSession()->getData(self::SHARE_VARIABLE))
			$this->_redirect = 'react/share';
			
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
		$this->_redirect = 'react/index/email';	
	}
	
	public function getServices()
	{
		return Mage::getSingleton('react/services');
	}
	
	protected function getSession()
	{
		return Mage::getSingleton('customer/session');
	}
	
	public function __toString()
	{
		return $this->_redirect;
	}
}