<?php
class React_React_Block_Share extends Mage_Core_Block_Template
{
	public function _construct()
	{
		$defaults = Mage::getModel('react/share')->getDefaults();

		$this->setTemplate('react/share.phtml');
		$this->setDefaults($defaults);
	}

	public function getFormUrl()
	{
		return $this->getUrl('react/share/index');
	}
}