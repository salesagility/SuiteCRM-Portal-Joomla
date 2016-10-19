<?php

/**
 * Converts an Array to a SugarCRM-REST compatible name_value_list
 *
 * @param Array $data
 * @return Array
 */
function convertArrayToNVL( $data ){
	$return = array();
	foreach ( $data as $key => $value ) {
        $return[] = array('name' => $key, 'value' => $value);
    }
	return $return;
}

/**
 * Converts a SugarCRM-REST compatible name_value_list to an Array
 *
 * @param Array $data
 * @return Array
 */
function convertNVLToArray ( $data ){
	$return = array();
	foreach ( $data as $row ){
    	$return[$row['name']] = $row['value'];
    }
    return $return;
}

class sugarRestClient {

	/**
	 * Rest object
	 *
	 * @var string
	 */
	private $rest_url = "";

	/**
	 * SugarCRM User
	 *
	 * @var string
	 */
	 private $rest_user = "";

	 /**
	 * SugarCRM Pass
	 *
	 * @var string
	 */
	 private $rest_pass = "";

	/**
	 * SugarCRM Session ID
	 *
	 * @var string
	 */
	protected $sid = NULL;

	/**
	 * @param string $url Url to sugar's soap.php
	 * @return boolean
	 */
	public function __construct(){
        include_once 'components/com_advancedopenportal/models/advancedopenportal.php';
        $settings = AdvancedOpenPortalModelAdvancedOpenPortal::getSettings();
        $this->rest_url =  $settings->sugar_url."/service/v4_1/rest.php";
        $this->base_url =  $settings->sugar_url;
        $this->rest_user =  $settings->sugar_user;
        $this->rest_pass =  $settings->sugar_pass;
	}


	/**
	 * convert to rest request and return decoded array
	 *
	 * @return array
	 */
	private function rest_request($call_name, $call_arguments) {

		ob_start();
		$ch = curl_init();

		$post_data = array(
            		'method' => $call_name,
		            'input_type' => 'JSON',
		            'response_type' => 'JSON',
		            'rest_data' => json_encode($call_arguments)
		);

        curl_setopt($ch, CURLOPT_URL, $this->rest_url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch ,CURLOPT_ENCODING,'gzip');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $output = curl_exec($ch);

        $response_data = json_decode($output, true);


		curl_close($ch);

		ob_end_flush();
		return $response_data;
	}

	/**
	 * Login with user credentials
	 *
	 * @param string $user
	 * @param string $password_hash
	 * @param boolean $admin_check
	 * @return boolean
	 */
	public function login(){
		$login_params = array(
			'user_name' => $this->rest_user,
			'password'  => $this->rest_pass,
		);

		$result = $this->rest_request( 'login', array(
			'user_auth' => $login_params,
            "application_name" => "",
			'name_value_list' => array(array('name' => 'notifyonsave', 'value' => 'true'))
		));

		if ( isset($result['id'] )){
			$this->sid = $result['id'];
			return $result['id'];
		} else if(isset($result['name'])) {
			return false;
		}
        return false;
	}

	/**
	 * Logout
	 */
	public function logout(){
		$this->rest_request('logout', array(
			'session'	=> $this->sid,
		));

		$this->sid = null;
	}

	/**
	 * Retrieves a list of entries
	 *
	 * @param string $module
	 * @param query $query
	 * @param string $order_by
	 * @param integer $offset
	 * @param array $select_fields
	 * @param integer $max_results
	 * @param boolean $deleted
	 * @return array
	 */
	public function getEntryList( $module, $query = '', $order_by = '', $offset = 0, $select_fields = array(), $related_fields = array(), $max_results = '0', $deleted = false ){
		if ( !$this->sid ) {
            return false;
        }

		$result = $this->rest_request('get_entry_list', array(
			'session'		=> $this->sid,
			'module_name'	=> $module,
			'query'		    => $query,
			'order_by'		=> $order_by,
			'offset'		=> $offset,
			'select_fields'	=> $select_fields,
			'link_name_to_fields_array' => $related_fields,
			'max_results'	=> $max_results,
			'deleted'		=> $deleted,
		));

		if ( $result['result_count'] > 0 ){
			return $result;
		} else {
			return FALSE;
		}
	}

    public function getEntry( $module, $id, $select_fields = array(), $related_fields = array() ){
        if ( !$this->sid ) {
            return false;
        }

        $result = $this->rest_request('get_entry', array(
            'session'		=> $this->sid,
            'module_name'	=> $module,
            'id'		    => $id,
            'select_fields'	=> $select_fields,
            'link_name_to_fields_array' => $related_fields,
        ));

        if ( !isset($result['result_count']) || $result['result_count'] > 0 ){
            return $result;
        } else {
            return FALSE;
        }
    }

	/**
	 * Adds or changes an entry
	 *
	 * @param string $module
	 * @param array $data
	 * @return array
	 */
	public function setEntry( $module, $data ){
		if ( !$this->sid ) {
            return false;
        }

    	$result = $this->rest_request( 'set_entry' , array(
    		'session' 			=> $this->sid,
    		'module_name'		=> $module,
    		'name_value_list'	=> convertArrayToNVL( str_replace("&", "%26", $data) ),
    	));

    	return $result;
	}

    /**
     * Creates a new relationship-entry
     *
     * @param string $module1
     * @param string $module1_id
     * @param string $module2
     * @param string $module2_id
     * @return array
     */
    public function setRelationship( $module1, $module1_id, $module2, $module2_id ){
		if ( !$this->sid ) {
            return false;
        }

    	$data = array(
    		'session' 	=> $this->sid,
    		'module_name' => $module1,
    		'module_id'	=> $module1_id,
    		'link_field_name' => $module2,
    		'$related_ids'=> array($module2_id),
    	);

    	$result = $this->rest_request('set_relationship',$data);

		return $result;
	}

    /**
     * Retrieves relationship data
     *
     * @param string $module_name
     * @param string $module_id
     * @param string $related_module
     * @return array
     */
    public function getRelationships( $module_name, $module_id, $related_module, $related_module_query = '', $related_fields = array(), $related_module_link_name_to_fields_array = array(), $deleted = false, $order_by = '', $offset = 0, $limit = false){
    	$result = $this->rest_request( 'get_relationships', array(
    		'session' => $this->sid,
    		'module_name' => $module_name,
    		'module_id'	=> $module_id,
    		'link_field_name' => $related_module,
    		'related_module_query' => $related_module_query,
    		'related_fields' => $related_fields,
    		'related_module_link_name_to_fields_array' => $related_module_link_name_to_fields_array,
    		'deleted' => $deleted,
            'order_by' => $order_by,
            'offset' => $offset,
            'limit' => $limit,
    	));

    	if ( !isset($result['error']['number']) || $result['error']['number'] == 0 ){
    		return $result;
    	}else{
    		return FALSE;
    	}
    }

    	/**
	 * Retrieves a module field
	 *
	 * @param string $module
	 * @param string $field
	 * @return field
	 */
	public function getModuleFields( $module, $field){
		if ( !$this->sid ) {
            return false;
        }

		$result = $this->rest_request('get_module_fields', array(
			'session'		=> $this->sid,
			'module_name'		=> $module,
		));

		if ( $result > 0 ){
			return $result['module_fields'][$field];
		} else {
			return FALSE;
		}
	}

    public function getAllModuleFields( $module){
        if ( !$this->sid ) {
            return false;
        }

        $result = $this->rest_request('get_module_fields', array(
            'session'		=> $this->sid,
            'module_name'		=> $module,
        ));

        if ( $result > 0 ){
            return $result['module_fields'];
        } else {
            return FALSE;
        }
    }

	public function get_note_attachment($note_id) {
        if ( !$this->sid ) {
            return false;
        }

			$call_arguments = array(
			'session' => $this->sid,
			'id' => $note_id
			);

			$result = $this->rest_request('get_note_attachment',
				$call_arguments
				);

			return $result;
    }

    public function set_note_attachment($note_id, $file_name, $file_location){
        if ( !$this->sid ) {
            return false;
        }

        $result = $this->rest_request( 'set_note_attachment' , array(
            'session'                   => $this->sid,
            'note' => array(
                'id' => $note_id,
                'filename' => $file_name,
                'file' => base64_encode(file_get_contents($file_location)),
            ),
        ));

        return $result;
    }

    public function get_document_revision($id){
        if ( !$this->sid ) {
            return false;
        }

        $result = $this->rest_request( 'get_document_revision' , array(
            'session' => $this->sid,
            'id'	  => $id,
        ));

        return $result;
    }

    public function set_document_revision($document_id, $file_name, $file_location, $revision_number = 1){
        if ( !$this->sid ) {
            return false;
        }

        $result = $this->rest_request( 'set_document_revision' , array(
            'session' 			=> $this->sid,
            'document_revision' => array(
                'id' => $document_id,
                'revision' => $revision_number,
                'filename' => $file_name,
                'file' => base64_encode(file_get_contents($file_location)),
            ),
        ));

        return $result;


    }
}
?>
