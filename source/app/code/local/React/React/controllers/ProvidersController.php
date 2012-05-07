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

	public function removeAction()
	{
		$service = Mage::getSingleton('react/services');
		$provider = $this->getRequest()->getParam('provider');
		$service->userRemoveProvider($this->_getCustomer()->getId(),$provider);

		$this->_redirect('react/providers');
	}

	private function _getCustomer()
	{
		return Mage::getSingleton('customer/session')->getCustomer();
	}
}