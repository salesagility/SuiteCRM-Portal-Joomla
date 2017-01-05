<?php

include_once 'components/com_advancedopenportal/models/SugarObject.php';
class SugarCase extends SugarObject{
    public function __construct($object,$relations){
        parent::__construct($object,$relations);
        $this->description = nl2br($this->description);
        $this->resolution = nl2br($this->resolution);
        include_once 'components/com_advancedopenportal/models/SugarCasesConnection.php';
        $caseConnection = SugarCasesConnection::getInstance();
        $this->status_display = $caseConnection->getCaseStatusDisplay($this->status);
    }
}