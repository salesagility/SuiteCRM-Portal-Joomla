<?php
include_once 'components/com_advancedopenportal/models/SugarObject.php';

/**
 * Class SugarUpdate
 */
class SugarUpdate extends SugarObject{

    /**
     * @var string
     */
    public static $MODULE = 'AOP_Case_Updates';
    /**
     * @var array
     */
    public static $REQUIRED = ['id', 'name'];

    /**
     * @var array
     */
    private $fields = array('id','name','date_entered','date_modified','description','contact','contact_id', 'internal', 'assigned_user_id');

    /**
     * @return string
     */
    public function toJson()
    {
        $data = $this->getFieldsToSave();
        return json_encode($data, true);
    }

    /**
     *  Loads the required data to display the update
     */
    public function loadDisplayData($parent = null)
    {
        $this->loadContact($parent);
        $this->loadRelationshipDetails('notes');
    }
}