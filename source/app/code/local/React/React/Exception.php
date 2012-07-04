<?php
class React_React_Exception extends Mage_Core_Exception
{
	/** 
		Construct function for exceptions.
 	*/
	public function __construct($message, array $params = array(), $cause = null, $code = 0)
	{
		$admin_code = Mage_Core_Model_Store::ADMIN_CODE;
		$_helper = Mage::helper('react');
		$message = (string)end($params);
		
		if (Mage::app()->getStore()->getCode() == $admin_code)
		{
			Mage::getSingleton('adminhtml/session')->addError($_helper->__($message));
			$response = Mage::app()->getResponse()->setRedirect(Mage::getUrl('*/customer'))->sendResponse();		
		}
		elseif ($_helper->isAjax())
		{
			$response = Mage::getModel('react/ajax_response');
			$response->addMessage($message);
			$response->addMessage($_helper->__('Please contact site administrator.'));
			$response->sendResponse(Mage::app()->getResponse());
			Mage::app()->getResponse()->sendResponse();
		}
		else 
		{
			$_helper->getSession()->addError($_helper->__($message))->addError($_helper->__('Please contact site administrator.'));
			$response = Mage::app()->getResponse()->setRedirect(Mage::getUrl('customer/account'))->sendResponse();
		}	
		die();
	}
}