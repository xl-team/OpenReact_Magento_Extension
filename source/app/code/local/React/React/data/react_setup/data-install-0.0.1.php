<?php
$_helper = Mage::helper('react');
$local_key = base64_encode(Mage::getBaseUrl());
$config = Mage::getModel('core/config')->saveConfig($_helper::XML_PATH_LOCAL_KEY, $local_key); 