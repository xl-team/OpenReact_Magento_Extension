<?php

class React_React_Helper_Client_Magentoservices extends React_React_Helper_Client_Services
{
    public function __construct()
    {
        $endpoints = array(
	       'http://social.react.com/XmlRpc_v2' => array('OAuthServer', 'Twitter', 'Facebook', 'Hyves', 'LinkedIn'),
	       'http://share.react.com/XmlRpc_v2' => array('Share'),
        );
        $auth = array_values(Mage::getStoreConfig('react/settings'));
        parent::__construct($endpoints, null, $auth);
        
    }
}