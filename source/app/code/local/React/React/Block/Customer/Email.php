<?php
class React_React_Block_Customer_Email extends Mage_Directory_Block_Data
{
	const NO_EMAIL_VARIABLE = 'react_no_email';


	public function _construct()
	{
		$this->setTemplate('react/email.phtml');

		parent::_construct();
	}

	public function getProvider()
	{
		$data = Mage::getSingleton('customer/session')->getData(self::NO_EMAIL_VARIABLE);
		return isset($data['connectedWithProvider']) ? $data['connectedWithProvider'] : '';
	}
}