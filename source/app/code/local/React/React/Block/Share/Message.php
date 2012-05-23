<?php
class React_React_Block_Share_Message extends Mage_Core_Block_Template
{
	public function _construct()
	{
		$this->setTemplate('react/share/message.phtml');
	}

	public function getLoginForm()
	{
		if ($this->getSession()->isLoggedIn())
			return '';
		
		$login_block = $this->getLayout()->createBlock('react/login','react_login',array(
			'title' => $this->__('Please login before sharing.'),
			'description' => '',
			'show_button' => false,
			'heading_tag' => 'h2',
		));
		
		return $login_block->toHtml();
	}		

	public function getFormAction()
	{
		return $this->getUrl('react/share/messagePost');
	}

	public function getSession()
	{
		return Mage::getSingleton('customer/session');
	}
}