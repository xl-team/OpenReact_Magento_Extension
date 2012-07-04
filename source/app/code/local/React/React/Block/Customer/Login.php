<?php
class React_React_Block_Customer_Login extends Mage_Core_Block_Template
{
	protected function _construct()
	{
		$_helper = Mage::helper('react');
		$this->setTemplate('react/customer/login.phtml');
	
		$this->setTitle($this->__('Login or register with your social network'));
		$this->setFieldset(false);
		$this->setHeadingTag('h1');
		$this->setShowButton(true);
		#$helper->setRedirect($this->_getRefererUrl()); 
		$this->setIsAjax($_helper->isAjax());
		parent::_construct();
	}

	public function isAjax()
	{
		return $this->getIsAjax();
	}

	public function getProviders()
	{
		return Mage::helper('react')->getProviders();
	}

	public function getLoginUrl()
	{
		return $this->getBaseUrl() . 'react/index/login/provider/';
	}

	public function getCssClass()
	{
		return ($this->getFieldset()) ? 'fieldset' : 'content';
	}

	public function getDescription()
	{
		if (!isset($this->_data['description']))
			return Mage::getStoreConfig('react/settings/description');

		return $this->_data['description'];
	}

	public function showButton()
	{
		return (bool)$this->getShowButton();
	}
	
	public function getEmailBlock()
	{
		$_helper = Mage::helper('react');
		$_html = '';
		if($_helper->getSession()->getData($_helper::VAR_NOEMAIL))
		{
			$html = $_helper->getDialogBoxHtml();
			$html .= $this->getLayout()->createBlock('react/customer_email')->toHtml();
		}
		return $html;
	}
	
}