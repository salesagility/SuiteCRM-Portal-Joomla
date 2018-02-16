<?php

include_once 'components/com_advancedopenportal/models/SugarNote.php';

use SuiteCRMRestClient\SuiteCRMRestClient;

/**
 * Class SugarObject
 */
class SugarObject
{

    /**
     * @var string
     */
    protected static $MODULE = '';

    /**
     * @var array
     */
    protected static $REQUIRED = [];

    /**
     * @var string
     */
    protected static $JSON_API_TYPE_KEYWORD = 'KEYWORD_RECORD_TYPE';

    /**
     * @var array
     */
    private $date_fields = ["date_entered", "date_modified"];

    /**
     * @var string
     */
    private $format = "d/m/Y H:i";

    /**
     * SugarObject constructor.
     * @param $record
     */
    protected function __construct(Array $record)
    {
        foreach ($record['data']['attributes'] as $name => $value) {
            if (in_array($name, $this->date_fields)) {
                $displayName = $name . "_display";
                $this->$displayName = date($this->format, strtotime($value));
            }
            // Convert the reserved type keyword alias
            if ($name === static::$JSON_API_TYPE_KEYWORD) {
                $name = 'type';
            }

            $this->$name = $value;
        }
        if (count($record['data']['relationships'])) {
            foreach ($record['data']['relationships'] as $relationName => $relation) {
                $this->$relationName = $relation;
            }
        }
        if (isset($this->description)) {
            $this->description = nl2br(html_entity_decode($this->description));
        }
        $this->id = $record['data']['id'];
    }

    /**
     * @return string
     */
    public static function getModuleName()
    {
        return static::$MODULE;
    }

    /**
     * Checks if this record is related to another
     *
     * @param $id
     * @param string $relationship
     * @return bool
     */
    public function isRelatedTo(String $id, String $relationship)
    {
        $this->loadRelationships($relationship);
        foreach ($this->$relationship as $related) {
            if ($id == $related['id']) {
                return true;
            }
        }
        return false;
    }

    /**
     *  Load the relationship ids
     *
     * @param string $relationship
     * @param string $module
     * @param string $id
     * @return mixed
     */
    public function loadRelationships(String $relationship, String $module = '', String $id = '')
    {
        $module = $module ? $module : static::$MODULE;
        $id = $id ? $id : $this->id;
        $this->$relationship = SuiteCRMRestClient::getInstance()->getRelationships($module, $id, $relationship);
        return $this->$relationship;
    }

    /**
     * Load the relationship with all of the related objects
     *
     * @param string $relationship
     * @param string $module
     * @param string $id
     * @return mixed
     */
    public function loadRelationshipDetails(String $relationship, String $module = '', String $id = '')
    {
        $this->loadRelationships($relationship, $module, $id);
        foreach ($this->$relationship as $key => $data) {
            $record = SuiteCRMRestClient::getInstance()->getEntry($data['type'], $data['id']);
            $this->$relationship[$key] = self::fromRelation($record);
        }
        return $this->$relationship;
    }

    /**
     * @param array $record
     * @return SugarObject
     */
    public function fromRelation($record)
    {
        if ($record['data']['type'] == 'AOP_Case_Updates') {
            return new SugarUpdate($record);
        }
        if ($record['data']['type'] == 'Notes') {
            return new SugarNote($record);
        }
        if ($record['data']['type'] == 'Cases') {
            return new SugarCase($record);
        }
        return SugarObject::fromScratch($record);
    }

    /**
     * Create a new object from data
     *
     * @param $data
     * @return SugarObject
     */
    public static function fromScratch(Array $data)
    {
        return new static(
            [
                'data' => [
                    'attributes' => $data,
                    'id' => $data['id']
                ]
            ]
        );
    }

    /**
     * Loads the related contact
     */
    public function loadContact($parent = null)
    {
        if (!empty($this->contact_id)) {
            if ($parent && $this->contact_id === $parent->poster->id) {
                $this->poster = $parent->poster;
            } else {
                $this->poster = SugarContact::fromID($this->contact_id);
            }
        }
    }

    /**
     * Create a new object from an ID by getting the data from REST
     *
     * @param $id
     * @return SugarObject
     */
    public static function fromID(String $id)
    {
        $data = SuiteCRMRestClient::getInstance()->getEntry(static::$MODULE, $id);

        if ($data === null) {
            return null;
        }

        return new static($data);
    }

    /**
     * @param array $files
     */
    public function addFiles(Array $files)
    {
        foreach ($files as $file_name => $file_location) {
            $note = SugarNote::fromScratch(
                [
                    'name' => "Case Attachment: $file_name",
                ]
            );
            $note->addAttachment($file_name, $file_location);
            $note = $note->save();
            $this->addRelationship('notes', $note->id);
            $note->addRelationship("contact", $this->contact_id);
        }
    }

    /**
     * Saves the object to SuiteCRM
     *
     * @return bool|SugarObject
     */
    public function save()
    {
        $data = $this->getFieldsToSave();
        $result = SuiteCRMRestClient::getInstance()->setEntry(static::$MODULE, $data);
        if ($result) {
            return new static($result);
        }
        return false;
    }

    /**
     * Transforms the fields to an array and sets any required fields which are not yet defined
     *
     * @return array
     */
    public function getFieldsToSave()
    {
        $data = [];
        foreach ($this as $var => $val) {
            // Convert the reserved type keywords to an alias
            if ($var == 'type') {
                $var = static::$JSON_API_TYPE_KEYWORD;
            }
            $data[$var] = $val;
        }
        foreach (static::$REQUIRED as $required) {
            if (!isset($data[$required])) {
                $data[$required] = '';
            }
        }
        return $data;
    }

    /**
     * @param string $relationship
     * @param string $relatedId
     * @return $this
     */
    public function addRelationship(String $relationship, String $relatedId)
    {
        SuiteCRMRestClient::getInstance()->setRelationship(static::$MODULE, $this->id, $relationship, $relatedId);
        return $this;
    }
}