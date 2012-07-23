<?php
class React_React_IndexController extends Mage_Core_Controller_Front_Action
{
	/**
		There is no index action.
	*/
	public function indexAction()
	{
		$this->_redirectReferer();
	}
	
	/**
 		Start the login process
 	*/
	public function loginAction()
	{
		$_helper = Mage::helper('react');
		$response = Mage::getModel('react/ajax_response');
		$response->setUrl($this->_getRefererUrl());
		
		if($session_url = $_helper->getSessionRedirect())
			$referer = $session_url;
		else
			$referer = $this->_getRefererUrl();
		if (!$_helper->isLoggedIn())
		{
			$provider = $this->getRequest()->getParam('provider');
			$result = $_helper->login($provider, $referer);
			if (isset($result['redirectUrl']));
				$response->setUrl($result['redirectUrl']);
		}
		$response->sendResponse($this->getResponse());
	}
	
	/**
		Processes the information after the tokenRequest method
 	*/
 	public function processAction()
 	{
		$_helper = Mage::helper('react/oauth');
		if ($_helper->isLoggedIn())
		{
			$status = $_helper->addNewProvider();	
			$this->_redirect('react/providers');
		}
		else 
		{
			if($session_url = $_helper->getSessionRedirect(false))
				$referer = $session_url;
			else
				$referer = $this->_getRefererUrl();
			$_helper->getSession()->setData($_helper::VAR_SESSION_CONFIRM_MAIL, $referer);
			
			$status = $_helper->processLoginRequest();
			$this->_redirectUrl($_helper->getSessionRedirect());
		}
	 }
	 	
	/**
 		Form processing for the "no email" page  
 	*/
	public function emailPostAction()
	{
	
		$_helper = Mage::helper('react/oauth');
		$response = Mage::getModel('react/ajax_response');
		
		$result = $_helper->getSession()->getData($_helper::VAR_NOEMAIL, true);
		if (!$result)
			return;
		
		if($session_url = $_helper->getSessionRedirect())
			$referer = $session_url;
		else
			$referer = $this->_getRefererUrl();
		$_helper->getSession()->setData($_helper::VAR_SESSION_CONFIRM_MAIL, $referer);	
		
		$result['profile']['email'] = $this->getRequest()->getParam('email');
		$status = $_helper->processLoginRequest($result);
		if ($status === false)
		{
			$response->addMessage($this->__('We are sorry you can not connect using %s.', $result['connectedWithProvider']));
		}
		else if (is_string($status))
		{
			$response->addMessage($status);		
		}
		/*else if ($_helper->getSession()->getData($_helper::SHARE_VARIABLE))
		{
			$this->_redirect('react/share');
		}*/
		else 
		{
			$response->setUrl(true);
		}
		$response->sendResponse($this->getResponse());
	}
	/**
		Retrives the email form for ajax requests
	*/
	public function emailFormAction()
	{
		$response = Mage::getModel('react/ajax_response');
		$block = $this->getLayout()->createBlock('react/customer_email');
		$response->setHtml($block->toHtml());
		$response->sendResponse($this->getResponse());
	}
	
	/**
		Clears the session variables
 	*/
	public function clearAction()
	{
		Mage::helper('react')->clearSession();
	} 
}