<?php
class React_React_ProvidersController extends Mage_Core_Controller_Front_Action
{	
	/**
		The customer providers page
	*/
	public function indexAction()
	{
		if (!Mage::helper('customer')->isLoggedIn())
		{
			$this->_redirect('customer/account');
			return;
		}
			
		$this->loadLayout();
		$this->_initLayoutMessages('customer/session');
		$this->_initLayoutMessages('catalog/session');
		$this->getLayout()->getBlock('head')->setTitle($this->__('Social Accounts'));

		$this->renderLayout();
	}

	/**
 		Add provider action
 	*/
	public function addAction()
	{
		$_helper = Mage::helper('react');
		$provider = $this->getRequest()->getParam('provider');
		$result = $_helper->login($provider, $this->_getRefererUrl());
		if (isset($result['redirectUrl']))
			$this->_redirectUrl($result['redirectUrl']);
		else 
			$this->_redirectReferer();
	}	
	
	/**
 		Remove provider action
 	*/
	public function removeAction()
	{
		$_helper = Mage::helper('react/oauth');
		$provider = $this->getRequest()->getParam('provider');
		$_helper->userRemoveProvider($_helper->getCustomer(), $provider);
		$_helper->getSession()->addNotice($this->__('You have successfully disconected your %s account.', $provider));
		$_helper->resetConnectedProviders($_helper->getCustomer());
		$this->_redirectReferer();
	}
}