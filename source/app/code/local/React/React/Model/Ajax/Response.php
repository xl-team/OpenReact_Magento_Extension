<?php
class React_React_Model_Ajax_Response extends Varien_Object
{
	/**
 		Magento internal construct
 	*/
	public function _construct()
	{
		$data = array(
			'url' => false,
			'html' => false,
			'messages' => array(),
			'error' => false,
 		);

		$this->addData($data);
	}
	
	public function addMessage($message = null)
	{
		$messages = $this->getMessages();
		if($message)
			$messages[] = (string)$message;
		$this->setMessages($messages);
	}
	
	/**
 		Set the body and the headers for the response
 		
 		Parameters: 
 			response - (Mage_Core_Controller_Response_Http) The HTTP response object
 	*/
 	
 	public function sendResponse(Mage_Core_Controller_Response_Http $response)
 	{
	 	if($html = $this->_getMessagesHtml())
	 		$this->setHtml($html);
		
	 	$response->setBody($this->__toJson());
		$response->setHeader('Content-Type', 'application/json', true);
 		#$response->sendResponse();
	 }
	 
	 /**
     	Generates the HTML for the messages;
     	
     	Returns:
     		(string) The HTML
	 */
	 
	 protected function _getMessagesHtml()
	 {
	 	$html = '';
	 	if($messages = $this->getMessages())
 		{
 	 		$block = Mage::app()->getLayout()->createBlock('core/template')->setTemplate('react/ajax/errors.phtml');
	 		$block->setErrors($this->getMessages());
	 		$html = $block->toHtml();
		}
		$this->unsetData('messages');
		return $html;
	}
}