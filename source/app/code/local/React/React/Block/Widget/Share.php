<?php 
class React_React_Block_Widget_Share extends React_React_Block_Share implements Mage_Widget_Block_Interface
{
	
	public function getDefaults()
	{
		$defaults = parent::getDefaults();
		$data = $defaults->getData();
		foreach($data as $key => &$default)
		{
			if($val = trim($this->getData($key)))
				$default = $val;
		}
		$defaults->addData($data);
		return $defaults;
	}
	
	protected function _toHtml()
	{
		if($this->getData('force'))
			Mage::unregister(self::VAR_DISPLAYED);
		$html = parent::_toHtml();
		if($this->getData('force'))
			Mage::unregister(self::VAR_DISPLAYED);
		return $html;
	}
}