<?php
include_once 'components/com_advancedopenportal/models/SugarObject.php';

/**
 * Class SugarContact
 */
class SugarContact extends SugarObject{

    /**
     * @var string
     */
    public static $MODULE = 'Contacts';
    /**
     * @var array
     */
    public static $REQUIRED = ['id', 'last_name'];

    /**
     * @var array
     */
    private $fields = array('id','first_name','last_name','date_entered','date_modified','description','portal_user_type','account_id');

    /**
     * @return mixed
     */
    public function getCases()
    {
        if($this->portal_user_type == 'Account'){
            return $this->loadRelationshipDetails('cases', 'Accounts', $this->account_id);
        }
        return $this->loadRelationshipDetails('cases');
    }
}