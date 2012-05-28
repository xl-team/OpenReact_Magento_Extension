<?php
class React_React_ShareController extends Mage_Core_Controller_Front_Action
{
	const SHARE_VARIABLE = 'react_share_info';
	
	public function indexAction()
	{
		$_helper = Mage::helper('react/process');
		
		$post = $this->getRequest()->getPost();
		$data = $this->getSession()->getData(self::SHARE_VARIABLE,true);
		if (!is_array($data))
			$data = array();
		
		$data = array_merge($data,$post);
		if (!$data) 
		{
			$this->_redirectUrl(Mage::getBaseUrl());
			return;
		}
		else if (empty($data['message']) || !$_helper->canShare())
		{
			$this->getSession()->setData(self::SHARE_VARIABLE,$post);
			$this->_redirect('react/share/message');
			return;
		}
	
		$message = Mage::getModel('react/share_message')->init($data);
		$status = Mage::getModel('react/share')->postMessage($message);

		if ($status)
			$this->getSession()->addSuccess($this->__('Page the page was successfully shared.'));
		else
			$this->getSession()->addError($this->__('An error has occured while trying trying to share this page.'));
		
		$this->_redirectUrl($data['url']);
	}
	
	public function messageAction()
	{
		$this->loadLayout();
		$this->_initLayoutMessages('customer/session');
		$this->_initLayoutMessages('catalog/session');
		$this->getLayout()->getBlock('head')->setTitle($this->__('Share Message'));
	
		$this->renderLayout();
	}
	
	
	public function messagePostAction()
	{
		$_helper = Mage::helper('react/process');
		$post = $this->getRequest()->getPost();
		if (empty($post['social_network']) && !$this->getSession()->isLoggedIn())
		{
			$this->getSession()->addError($this->__("You need to login in order to share."));
			$this->_redirect('*/*/message');
			return;
		}
		 
		else if (empty($post['message'])) 
		{
			$this->getSession()->addError($this->__("Please add a message."));
			$this->_redirect('*/*/message');	
			return;
		}		
		
		$data = $this->getSession()->getData(self::SHARE_VARIABLE,true);
		$data['message'] = $post['message'];
		$this->getSession()->setData(self::SHARE_VARIABLE,$data);		
		if ($post['social_network'])
		{
			$result = $_helper->getServices()->tokenRequest($post['social_network']);				
			if (isset($result['redirectUrl']))
				$this->_redirectUrl($result['redirectUrl']);	
		}
	 	else 
		{
			$this->_redirect('*/*');
		}		
	}
	
	public function processAction()
	{
		$_helper = Mage::helper('react/process');
		
		$result = $_helper->getServices()->tokenAccess($this->getRequest()->getParams());
		$status =  $_helper->processRequest($result);
		if (!$status)
			$this->getSession()->addError($this->__('We are sorry you can not connect using %s',$result['connectedWithProvider']));	
		
		$this->_redirect($_helper->getRedirect());
	}
	
	public function getSession()
	{
		return Mage::getSingleton('customer/session');	
	}
}