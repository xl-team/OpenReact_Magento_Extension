<?php
class React_React_Model_Share extends Varien_Object
{
	public function _construct()
	{
		$data = array(
			'client' => Mage::helper('react/client_magentoservices'),
			'defaults' => null
		);
		
		$this->setData($data);
	}

	public function postMessage(Mage_React_React_Share_Message $message)
	{
		
		if ($message->getApplicationUserId())
		{
		 	$status = $this->getClient()->Share->postMessage(
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

	public function getDefaults()
	{
		
		if (is_null($this->getData('defaults')))
		{
			$defaults = Mage::getModel('react/share_message');
			if (!$this->getAction())
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
					if(is_object($img))
						$defaults->setImg($img->getUrl());
				break;
				
				case 'category':
					$_category = Mage::registry('current_category');
					
					$defaults->setUrl($_category->getUrlPath());
					$defaults->setTitle($_category->getName()); 
					$defaults->setDescription(nl2br(strip_tags($_category->getDescription())));
					
					$img = $this->getCategoryThumbnail($_category);
					if ($img)
						$img = $_category->getImageUrl();
					$defaults->setImg($img);
				break;
				
				case 'page':
					$_page = Mage::getSingleton('cms/page'); 	
					
					$defaults->setUrl(Mage::helper('cms/page')->getPageUrl($_page->getId()));
					$defaults->setTitle($_page->getTitle());
					$defaults->setDescription($this->getExcerpt($_page));
					$defaults->setImg($this->getDefaultImage());
				break;
				
				default:
					$defaults->setUrl(Mage::helper('core/url')->getCurrentUrl());
					$defaults->setTitle($this->getDefaultTitle());
					$defaults->setDescription($this->getDefaultDescription());
					$defaults->setImg($this->getDefaultImage());
				break;
			}
			$this->setDefaults($defaults);
		}
		return $defaults;
	}
	
	protected function getExcerpt(Mage_Cms_Model_Page $page)
	{
		$content = strip_tags($page->getContent());
		$content_line = explode("\n",$content);
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
	
	protected function getCategoryThumbnail(Mage_Catalog_Model_Category $category)
	{
        $url = false;
        if ($thumbnail = $category->getThumbnail()) {
            $url = Mage::getBaseUrl('media').'catalog/category/'.$thumbnail;
        }
        return $url;
	}
	
	protected function getDefaultTitle()
	{
		return htmlspecialchars(html_entity_decode(trim(Mage::getStoreConfig('design/head/default_title')), ENT_QUOTES, 'UTF-8'));
	}
	
	protected function getDefaultDescription()
	{
		return Mage::getStoreConfig('design/head/default_description');
	}
	
	protected function getDefaultImage()
	{ 
		return Mage::getDesign()->getSkinUrl(Mage::getStoreConfig('design/header/logo_src'));
	}
}	