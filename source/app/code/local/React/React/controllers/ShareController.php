<?php
class React_React_ShareController extends Mage_Core_Controller_Front_Action
{
	const SHARE_VARIABLE = 'react_share_info';


	public function indexAction()
	{
		$_helper = Mage::helper('react/share');
		$response = Mage::getModel('react/ajax_response');
		$post = $this->getRequest()->getPost();
		
		$_helper->getSession()->setData($_helper::VAR_SHARE, $post);
		if(!$_helper->isConnected($_helper->getCustomer()))
		{
			$block = $this->getLayout()->createBlock('react/customer_login');
			$block->setFieldset(1);	
			$block->setTitle($this->__('Social Account'));
			$block->setDescription($this->__('You need to connect with a social network before you can share this page.'));
			$response->setHtml($block->toHtml());
			$response->sendResponse($this->getResponse());
		}
	 	else 
	 	{
	 		$this->messageAction();		
	 	}
	}

	public function messageAction()
	{
		$_helper = Mage::helper('react/share');
		if(!$_helper->isConnected($_helper->getCustomer()))
			$this->indexAction();
		else
		{
			$response = Mage::getModel('react/ajax_response');
			$block = $this->getLayout()->createBlock('react/share_message');
			$response->setHtml($block->toHtml());
			$response->sendResponse($this->getResponse());
		}
	}

	public function messagePostAction()
	{
		$_helper = Mage::helper('react/share');
		$response = Mage::getModel('react/ajax_response');
		$post = $this->getRequest()->getPost();
		$data = $_helper->getSession()->getData($_helper::VAR_SHARE, true);
		if (!is_array($data))
			$data = array();
		$data['message'] = $post['message'];
		$data = array_merge($data, $post);
		$message = Mage::getModel('react/share_message')->init($data);
		$status = $_helper->postMessage($message);	
		
		if ($status)
		{
			foreach($status['errors'] as $provider => $data)
			{
				$response->addMessage($this->__('There was an error sharing through %s', $provider));
			}
			foreach($status['results'] as $provider => $data)
			{
				$response->addSuccess($this->__('You have successfully shared this page through %s', $provider));
			}	
		}
		else
			$response->addMessage($this->__('An error has occured while trying trying to share this page.'));
		$response->sendResponse($this->getResponse());
	}
}