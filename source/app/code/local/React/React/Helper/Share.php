<?php
class React_React_Helper_Share extends React_React_Helper_Data
{
	protected $_defaults = null;
	protected $_action = false;
	
	/**
		Sends the message (Shares on providers)
		
		Parameters: 
			message - (React_React_Model_Share_Message) The message model
			
		Returns:
			(boolean) TRUE if the message was sent successfully
	*/	
	public function postMessage(React_React_Model_Share_Message $message)
	{
		if ($message->getApplicationUserId())
		{
			$status = $this->_client->Share->postMessage(
				$message['application_user_id'],
				$message['message'],
				$message['providers'],
				$message['url'],
				$message['img'],
				$message['title'],
				$message['description']
			);
			return (bool)$status;
		}
 
		return false;
	}
	
	/**
		Creates the message defaults
	
		Returns:
			(array) An array containing the message defaults
	*/
	public function getDefaults()
	{
		if (is_null($this->_defaults))
		{
			$defaults = Mage::getModel('react/share_message');
			if (!$this->_action)
				$action = Mage::app()->getRequest()->getControllerName();
			switch ($action)
			{
				case 'product':
					$_product = Mage::registry('current_product');

					$defaults->setUrl($_product->getUrlInStore());
					$defaults->setTitle($_product->getName());
					$defaults->setDescription(nl2br(strip_tags($_product->getDescription())));

					$imgs = $_product->getMediaGalleryImages()->getItems();
					$img = array_shift($imgs);
					if (is_object($img))
						$defaults->setImg($img->getUrl());
					else 
						$defaults->setImg($this->_getDefaultImage());
					break;
				case 'category':
					$_category = Mage::registry('current_category');	
					$defaults->setUrl($_category->getUrl());
					$defaults->setTitle($_category->getName());
					$defaults->setDescription(nl2br(strip_tags($_category->getDescription())));

					$img = $this->_getCategoryThumbnail($_category);
					if (!$img)
						$img = $_category->getImageUrl();
					if (!trim($img))
						$img = $this->_getDefaultImage();
					$defaults->setImg($img);
					break;
				case 'page':
					$_page = Mage::getSingleton('cms/page');

					$defaults->setUrl(Mage::helper('cms/page')->getPageUrl($_page->getId()));
					$defaults->setTitle($_page->getTitle());
					$defaults->setDescription($this->_getExcerpt($_page));
					$defaults->setImg($this->_getDefaultImage());
					break;
				default:
					$defaults->setUrl(Mage::helper('core/url')->getCurrentUrl());
					$defaults->setTitle($this->_getDefaultTitle());
					$defaults->setDescription($this->_getDefaultDescription());
					$defaults->setImg($this->_getDefaultImage());
					break;
			}
			$this->_defaults = $defaults;
		}
		return $this->_defaults;
	}
	
	/**
		Parses the excerpt for a CMS page
		
		Returns:
			(string) The first line of the CMS page
	*/
	protected function _getExcerpt(Mage_Cms_Model_Page $page)
	{
		$content = strip_tags($page->getContent());
		$content_line = explode("\n", $content);
		$flag = true;

		foreach ($content_line as $line)
		{
			if (!trim($line))
				continue;

			if ($flag)
				$flag = false;
			else
				return $line;
		}

		return '';
	}

	/**
 		Retrives the category thumbnail URL
 		
 		Returns: 
 			(string) Category thumbnail URL
 	*/
	protected function _getCategoryThumbnail(Mage_Catalog_Model_Category $category)
	{
		$url = false;
		if ($thumbnail = $category->getThumbnail())
		{
			$url = Mage::getBaseUrl('media') . 'catalog/category/' . $thumbnail;
		}
		return $url;
	}
	
	/**
 		Retrives the default title for the message
 		
 		Returns:
 			(string) Store default title
 	*/
	protected function _getDefaultTitle()
	{
		return htmlspecialchars(html_entity_decode(trim(Mage::getStoreConfig('design/head/default_title')), ENT_QUOTES, 'UTF-8'));
	}
	
	/**
 		Retrives the default description for the message
 		
 		Returns:
 			(string) Store default description
 	*/
	protected function _getDefaultDescription()
	{
		return Mage::getStoreConfig('design/head/default_description');
	}
	
	/**
 		Retrives the default image for the message
 		
 		Returns:
 			(string) Store logo
 	*/
	protected function _getDefaultImage()
	{
		return Mage::getDesign()->getSkinUrl(Mage::getStoreConfig('design/header/logo_src'));
	}
}