<?php
include_once 'components/com_advancedopenportal/models/SugarObject.php';
class SugarUpdate extends SugarObject{

    public function __construct($case,$relations){
        parent::__construct($case,$relations);
        $this->description = nl2br(html_entity_decode($this->description));
        if(isset($this->contact)){
            $this->poster = $this->contact[0];
            $this->poster->type = "contact";
        }elseif($this->assigned_user_link){
            $this->poster = $this->assigned_user_link[0];
            $this->poster->type = "user";
        }
    }

}