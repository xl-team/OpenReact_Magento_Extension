<?php
class React_React_Block_Dialog extends Mage_Core_Block_Template
{
	public function _construct()
	{
		$this->setTemplate('react/dialog.phtml');
		$_helper = Mage::helper('react');
		$this->setConfirmEmail($_helper->getSession()->getData($_helper::VAR_SESSION_CONFIRM_MAIL) && !$_helper->isConnected($_helper->getCustomer()));
	}
}