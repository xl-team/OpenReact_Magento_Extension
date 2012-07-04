<?php
class React_React_Block_Adminhtml_Customer_Edit_Tab_React 
 extends Mage_Adminhtml_Block_Template
 implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
	public function getTabLabel()
	{
		return Mage::helper('react')->__('Social Network Profiles');
	}
	
	public function getTabTitle()
	{
		return Mage::helper('react')->__('Social Network Profiles');	
	}
	
	 public function canShowTab()
    {
        if (Mage::registry('current_customer')->getId()) {
            return true;
        }
        return false;
    }

    public function isHidden()
    {
        if (Mage::registry('current_customer')->getId()) {
            return false;
        }
        return true;
    }
	
}