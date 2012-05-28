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
		$provider = $this->getRequest()->getParam('provider');		
		$result =  Mage::getModel('react/services')->tokenRequest($provider);
		
		if (isset($result['redirectUrl']))
			$this->_redirectUrl($result['redirectUrl']);
	}
	
	public function processAction()
	{
		$this->_redirect('react/providers');
		
		if (!$this->getSession()->isLoggedIn())	
			return;
	
		$service = Mage::getSingleton('react/services');
		$result = $service->tokenAccess($this->getRequest()->getParams());
		if (isset($result['reactOAuthSession']))
		{
			$service->tokenSetUserId($this->getCustomer()->getId(), $result['reactOAuthSession']);
			$this->getSession()->addSuccess($this->__('You have successfully connected your %s account.', $result['connectedWithProvider']));
			$service->resetConnectedProviders();
		}
		else 
		{
			$this->getSession()->addError($this->__('An error has occured while trying to connect your social account.'));
		}
	}
	
	public function removeAction()
	{
		
		$service = Mage::getSingleton('react/services');
		$provider = $this->getRequest()->getParam('provider');
		$service->userRemoveProvider($this->_getCustomer()->getId(),$provider);
		$this->getSession()->addNotice($this->__('You have success fully disconected your %s account.', $provider));
		$service->resetConnectedProviders();
		
		$this->_redirect('react/providers');
		
	}

	private function _getCustomer()
	{
		return Mage::getSingleton('customer/session')->getCustomer();
	}
	
	public function getSession()
	{
		return Mage::getSingleton('customer/session');
	}

	public function getCustomer()
	{
		return $this->getSession()->getCustomer();
	}
}