<?php
class React_React_Exception extends Mage_Core_Exception
{
	public function __construct($message, array $params = array(), $cause = null, $code = 0)
	{
		$_helper = Mage::helper('react');

		$message = (string)end($params);
		$this->getSession()->addError($_helper->__($message))->addError($_helper->__('Please contact site administrator.'));
		$response = Mage::app()->getResponse()->setRedirect(Mage::getUrl('customer/account'))->sendResponse();

		die();
	}

	public function getSession()
	{
		return Mage::getSingleton('core/session');
	}
}