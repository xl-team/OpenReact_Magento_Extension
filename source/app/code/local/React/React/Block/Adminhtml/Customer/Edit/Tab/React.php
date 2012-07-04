<?php
class React_React_Block_Adminhtml_Customer_Edit_Tab_React 
 extends Mage_Adminhtml_Block_Template
 implements Mage_Adminhtml_Block_Widget_Tab_Interface
{	
	/** 
 		Internal Magento constructor
 	*/
	public function _construct()
	{
		$this->setTemplate('react/customer.phtml');
	}
	
	public function getProviders()
	{
		$_helper = Mage::helper('react/profile');
		$customer = Mage::registry('current_customer');
		$customer_providers = ($customer->getId()) ? $_helper->getProfiles($customer) : array();
		return $customer_providers;
	}
	
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