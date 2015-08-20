<?php

class SugarObject {

    private $date_fields = array("date_entered","date_modified");
    private $format = "d/m/Y H:i";

    public function __construct($case, $relations = array()){
        foreach($case['name_value_list'] as $name_value){
            $name = $name_value['name'];
            $value = $name_value['value'];
            if(in_array($name,$this->date_fields)){
                $disp_name = $name . "_display";
                $this->$disp_name = date($this->format, strtotime($value));
            }

            $this->$name = $value;
        }
        foreach($relations as $relation){
            $relationname = $relation['name'];
            $newrecords = array();
            foreach($relation['records'] as $record){
                $new_record = new stdClass();
                foreach($record as $name_value){
                    $name = $name_value['name'];
                    $value = $name_value['value'];
                    $new_record->$name = $value;
                }

                $newrecords[] = $new_record;
            }
            $this->$relationname = $newrecords;

        }
    }

}