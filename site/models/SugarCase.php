<?php

include_once 'components/com_advancedopenportal/models/SugarObject.php';

class SugarCase extends SugarObject
{
    /**
     * SugarCase constructor.
     * @param $object
     * @param array $relations
     */
    public function __construct($object, $relations = array())
    {
        parent::__construct($object, $relations);
        $this->description = nl2br($this->description);
        include_once 'components/com_advancedopenportal/models/SugarCasesConnection.php';
        $caseConnection = SugarCasesConnection::getInstance();
        $this->status_display = $caseConnection->getCaseStatusDisplay($this->status);
    }
}