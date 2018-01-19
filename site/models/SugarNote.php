<?php

include_once 'components/com_advancedopenportal/models/SugarObject.php';

/**
 * Class SugarNote
 */
class SugarNote extends SugarObject{

    /**
     * @var string
     */
    public static $MODULE = 'Notes';
    /**
     * @var array
     */
    public static $REQUIRED = ['id', 'name', 'portal_flag'];

    /**
     * @var array
     */
    private $fields = array('id','name', 'date_entered','date_modified','description','filename','file_url');

    public function getNoteAttachment()
    {
        $attachment = SugarRestClient::getInstance()->get_note_attachment($this->id);
        return $attachment['note_attachment'];
    }
}