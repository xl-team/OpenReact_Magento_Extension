<?php
class React_React_Helper_Oauth extends React_React_Helper_Data
{	
	/**
 		Process the request for login. This function is called after the tokenRequest method
	  
	  	Paremeters: 
	  		Result - (array) The result of tokenAccess method. If not supplied it will execute a tokenAccess request.
 		
		 Returns:
	  		(boolean)|(null)|(string) - The status of the method | The error for AJAX response
	 */
 	public function processLoginRequest($result = null)
 	{
 		$customer = Mage::getModel('customer/customer');
		$customer_id = false;
	 	
	 	if(is_null($result)){
	 		$params = $this->_getRequest()->getParams();
 			$result = $this->_client->OAuthServer->tokenAccess($params, true);
 			$customer_id = $this->decodeApplicationUserId($result['applicationUserId']);
		}	
 		
	 	if ($customer_id) 
	 	{
			$customer->load($customer_id);
		}
		else
		{
			$customer->setData('website_id', Mage::app()->getStore()->getWebsiteId());
			$customer->loadByEmail($result['profile']['email']);
			if ($customer->getId())
			{
				#$this->getServices()->updateAccount($customer, $result);
				$error = $this->__('This email is all ready in use. Please login before connecting a new social account.');
				if($this->isAjax())
					return $error;
				
				$this->getSession()->addError($error);
				return true;
			}
			else
			{
				$status = $this->_createCustomer($customer, $result);
				if (!$status)
					return $status;
			}
		}
		
		$session_redirect = $this->getSessionRedirect();
		Mage::getSingleton('customer/session')->loginById($customer->getId());
		$this->setSessionRedirect($session_redirect);
		$this->getSession()->addSuccess($this->__('You have successfully logged in using your %s account.', $result['connectedWithProvider']));
		
		/** SHARE REDIRECT 
		if ($this->getSession()->getData(self::SHARE_VARIABLE))
			$this->_redirect('react/share');
		*/
		return true;
	}
	
	/**
	 * Adds new provider to an existing user, this function is called after the tokenRequest method
	 */
	public function addNewProvider()
	{
		$params = $this->_getRequest()->getParams();
		$result = $this->_client->OAuthServer->tokenAccess($params, false);
		
		if(isset($result['applicationUserId']) && $result['applicationUserId'])
		{
			$this->getSession()->addError($this->__('There is already an account with this email. Please contact site administrator.'));
		}
		else if (isset($result['reactOAuthSession']) && $this->getCustomer()->getId())
		{
			$this->_client->OAuthServer->tokenSetUserId($this->encodeApplicationUserId($this->getCustomer()), $result['reactOAuthSession']);
			$this->getSession()->addSuccess($this->__('You have successfully connected your %s account.', $result['connectedWithProvider']));
			$this->resetConnectedProviders($this->getCustomer());
		}
		else 
		{
			$this->getSession()->addError($this->__('An error has occured while trying to connect your social account.'));
		}
	}
	
	
	/**
		Implementation of the userGetProviders method
 
 		Parameters:
	 		customer - (Mage_Customer_Model_Customer) The customer model  
	 	
 		Returns:
 			connected_providers - (array) An array containing the providers of the current customer
 	*/
	public function userGetProviders(Mage_Customer_Model_Customer $customer)
	{
		$connected = $this->_client->OAuthServer->userGetProviders($this->encodeApplicationUserId($customer));
		return $connected['connectedWithProviders'];
	}
	
	/**
		Implementation of the userRemoveProvider method
		
		Parameters:
	 		customer - (Mage_Customer_Model_Customer) The customer model
			provider - (sting) The provider that will pbe removed
			event - (boolean) If set to TRUE it will bypass the "can remove provider" check
	*/
	public function userRemoveProvider(Mage_Customer_Model_Customer $customer, $provider = null, $event = false)
	{
		$flag = ($event) ? true : $this->canRemoveProvider($customer);
		if ($flag)
			$this->_client->OAuthServer->userRemoveProvider($this->encodeApplicationUserId($customer), $provider);
	}
		
	/**
		Helper function used to create a new customer
 
 		Parameters:
	 		customer - (Mage_Customer_Model_Customer) The customer model 
 		 	result - (array) The data returned by the tokenAccess method 
	 	
 		Returns:
 			(boolean) - TRUE if the creation of new cutomer succeeded 
 	*/
	protected function _createCustomer(Mage_Customer_Model_Customer $customer, array $result)
	{
		$profile = $result['profile'];

		if ($profile['email'])
		{
			$customer->setEmail($profile['email']);
			$customer->setPassword(Mage::helper('core')->getRandomString(6));
			$name = explode(' ', $profile['real_name'], 2);
			$customer->setFirstname($name[0]);

			if (isset($name[1]))
				$customer->setLastname($name[1]);

			$customer->setDob($profile['birthday']);

			switch ($profile['gender'])
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
				$this->_client->OAuthServer->tokenSetUserId($this->encodeApplicationUserId($customer), $result['reactOAuthSession']);

			return true;
		}
		else
		{
			$this->getSession()->setData(self::VAR_NOEMAIL, $result);
			return null;
		}
	} 
}