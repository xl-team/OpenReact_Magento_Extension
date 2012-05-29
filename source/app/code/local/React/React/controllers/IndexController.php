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
		
		if (Mage::helper('customer')->isLoggedIn())
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
		$_helper = Mage::helper('react/process');
		
		if (Mage::helper('customer')->isLoggedIn())
	 		return;
		
		$result = $_helper->getServices()->tokenAccess($this->getRequest()->getParams());
		$status = $_helper->processRequest($result);
		
		$this->_redirect($_helper->getRedirect());
			
		if (is_bool($status) && !$status)
			$this->getSession()->addError($this->__('We are sorry you can not connect using %s.',$result['connectedWithProvider']));	

		
	}
	
	public function emailAction()
	{
		$_helper = Mage::helper('react/process');
	
		if (!$this->getSession()->getData($_helper::NO_EMAIL_VARIABLE))
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
		
		$_helper = Mage::helper('react/process');
		$result = $this->getSession()->getData($_helper::NO_EMAIL_VARIABLE, true);
		if (!$result)		
			return;
			
		$result['profile']['email'] = $this->getRequest()->getParam('email');
		$status = $_helper->processRequest($result);
		if (!$status)
		{
			$this->getSession()->addError($this->__('We are sorry you can not connect using %s.', $result['connectedWithProvider']));	
			$this->getSession()->unsetData($_helper::NO_EMAIL_VARIABLE);
			return;			
		}
		else if ($this->getSession()->getData($_helper::SHARE_VARIABLE))
		{
			$this->_redirect('react/share');
		} 
	}
	
	public function getSession()
	{
		return Mage::getSingleton('core/session');
	}
}
?>