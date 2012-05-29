<?php

class React_React_ProvidersController extends Mage_Core_Controller_Front_Action {

	public function indexAction()
	{
		$this->loadLayout();
		$this->_initLayoutMessages('customer/session');
		$this->_initLayoutMessages('catalog/session');
		$this->getLayout()->getBlock('head')->setTitle($this->__('Social Accounts'));

		$this->renderLayout();
	}

	public function addAction()
	{
		$_helper = Mage::helper('react/process');
		$provider = $this->getRequest()->getParam('provider');		
		$result =  $_helper->getServices()->tokenRequest($provider);
		
		if (isset($result['redirectUrl']))
			$this->_redirectUrl($result['redirectUrl']);
	}
	
	public function processAction()
	{
		$this->_redirect('react/providers');
		
		if (Mage::helper('customer')->isLoggedIn())	
			return;
		
		$_helper = Mage::helper('react/process');
		$result = $_helper->getServices()->tokenAccess($this->getRequest()->getParams());
		
		if (isset($result['reactOAuthSession']))
		{
			$_helper->getServices()->tokenSetUserId($this->getCustomer()->getId(), $result['reactOAuthSession']);
			$this->getSession()->addSuccess($this->__('You have successfully connected your %s account.', $result['connectedWithProvider']));
			$_helper->getServices()->resetConnectedProviders();
		}
		else 
		{
			$this->getSession()->addError($this->__('An error has occured while trying to connect your social account.'));
		}
	}
	
	public function removeAction()
	{
		$_helper = Mage::helper('react/process');
		$provider = $this->getRequest()->getParam('provider');
		$_helper->getServices()->userRemoveProvider($this->getCustomer()->getId(),$provider);
		$this->getSession()->addNotice($this->__('You have success fully disconected your %s account.', $provider));
		$_helper->getServices()->resetConnectedProviders();
		
		$this->_redirect('react/providers');
		
	}

	public function getSession()
	{
		return Mage::getSingleton('core/session');
	}

	public function getCustomer()
	{
		return Mage::getSingleton('core/session')->getCustomer();
	}
}