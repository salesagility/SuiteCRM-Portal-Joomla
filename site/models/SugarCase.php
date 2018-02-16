<?php

include_once 'components/com_advancedopenportal/models/SugarObject.php';
include_once 'components/com_advancedopenportal/models/SugarNote.php';
include_once 'components/com_advancedopenportal/models/SugarUpdate.php';

/**
 * Class SugarCase
 *
 */
class SugarCase extends SugarObject{

    /**
     * @var string
     */
    public static $MODULE = 'Cases';

    /**
     * @var array
     */
    public static $REQUIRED = ['id', 'name', 'case_number', 'account_name'];

    /**
     * @var array
     */
    private $fields = array('id','name','date_entered','date_modified','description','case_number','type','status','state','priority','contact_created_by_id', 'contact_created_by_name');

    /**
     *  Loads the required data to display the case
     */
    public function loadDisplayData()
    {
        $this->poster = SugarContact::fromID($this->contact_created_by_id);
        $this->loadRelationshipDetails('aop_case_updates');
        $this->loadRelationshipDetails('notes');
        foreach ($this->aop_case_updates as $update){
            $update->loadDisplayData($this);
        }
    }

    /**
     * @param bool $oldState
     * @return bool
     */
    public function toggleState(bool $oldState)
    {
        if($oldState && SugarCasesConnection::isAllowedClosing()){
            $this->state = 'Closed';
            $this->status = 'Closed_Closed';
            return true;
        }
        if(!$oldState && SugarCasesConnection::isAllowedReopening()){
            $this->state = 'Open';
            $this->status = 'Open_New';
            return true;
        }
        return false;
    }

    /**
     * @return int
     */
    public function isOpen()
    {
        return strpos($this->state, 'Open') === 0 ? 1 : 0;
    }
}