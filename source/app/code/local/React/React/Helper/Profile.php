<?php
class React_React_Helper_Profile extends React_React_Helper_Oauth
{
	/**
		Fetches all the information available from the providers 
	
		Parameters:
			customer - (Mage_Customer_Model_Customer) The customer model
			
		Return:
			(array) An array containing all the information
	*/
	public function getProfiles(Mage_Customer_Model_Customer $customer)
	{
		$profiles = array();
		$providers = $this->userGetProviders($customer);
		$customer_id = $this->encodeApplicationUserId($customer);
		foreach($providers as $provider)
		{
			$function = '_get'.ucfirst($provider);
			$profiles[$provider] = $this->$function($customer_id);
			/*switch($provider)
			{
				case 'Facebook':
					$profiles['Facebook'] = $this->_client->Facebook->usersGetInfo($customer_id); break;		
				case 'Twitter':
					$profiles['Twitter'] = '';
				default:
					$profiles[$provider] = $this->__('There is no information available'); break;
			}*/
		}
		return $profiles;
	}
	
	/**
 		Retrivest the Google profile
		
		Parameters: 
 			customer_id - (string) The encoded customer ID
 			
		Returns:
			info - (array) Contianig the inforamtion provided by Google.
			 
 	*/
	/* NOT WORKING
	protected function _getGoogle($customer_id)
	{
		$info = $this->_client->Google->plusPeopleGet($customer_id, $customer_id);		
		return $info;
	}
	*/
	/**
 		Retrivest the Twitter profile
		
		Parameters: 
 			customer_id - (string) The encoded customer ID
 			
		Returns:
			info - (array) Contianig the inforamtion provided by Twitter.
			 
 	*/
	protected function _getTwitter($customer_id = null)
	{
		$info = $this->_client->Twitter->usersShow($customer_id);
		$info['picture'] = '<img src="'.$info['profile_image_url'].'" >';
		unset($info['id_str'], $info['name'], $info['protected'], $info['utc_offset'], $info['is_translator'], $info['profile_background_color'],
			$info['profile_background_tile'], $info['profile_background_image_ur'], $info['profile_background_image_url_https'], $info['profile_image_url'],
			$info['profile_image_url_https'], $info['profile_link_color'], $info['profile_sidebar_border_color'], $info['profile_sidebar_fill_color'], $info['profile_text_color'],
			$info['profile_use_background_image'], $info['show_all_inline_media'], $info['default_profile'], $info['default_profile_image'], $info['follow_request_sent'], $info['notifications'],
			$info['profile_background_image_url'], $info['geo_enabled']
		);
		
		return $info;
	}
				
	public function __call($name, $arguments)
	{
		return $this->__('There is no information available (yet)');
	}
	
}