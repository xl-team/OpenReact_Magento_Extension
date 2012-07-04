<?php
class React_React_Block_Customer_Email extends Mage_Directory_Block_Data
{
	public function _construct()
	{
		$this->setTemplate('react/customer/email.phtml');

		parent::_construct();
	}

	public function getProvider()
	{
		$_helper = Mage::helper('react');
		$data = Mage::getSingleton('customer/session')->getData($_helper::VAR_NOEMAIL);
		return isset($data['connectedWithProvider']) ? $data['connectedWithProvider'] : '';
	}
}