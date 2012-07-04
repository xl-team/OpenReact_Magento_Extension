<?php
class React_React_Block_Share extends Mage_Core_Block_Template
{
	const VAR_DISPLAYED = 'react_share_can_show'; 
	
	public function _construct()
	{
		$defaults = Mage::helper('react/share')->getDefaults();

		$this->setTemplate('react/share.phtml');
		$this->setDefaults($defaults);
	}

	public function getFormUrl()
	{
		return $this->getUrl('react/share/index');
	}
	
	protected function _toHtml()
	{
		$html = '';
		$displayed = Mage::registry(self::VAR_DISPLAYED);			
		if(!$displayed)
		{
			$html = parent::_toHtml();
			Mage::register(self::VAR_DISPLAYED, true, true);
		}
		return $html;
	}
}