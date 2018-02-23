<?php

include_once 'components/com_advancedopenportal/models/SugarObject.php';

/**
 * Class SugarNote
 */
class SugarNote extends SugarObject{

    /**
     * @var
     */
    private $file_name;

    /**
     * @var
     */
    private $file_location;

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

    /**
     * @return mixed
     */
    public function getNoteAttachment()
    {
        $attachment = \SuiteCRMRestClient\SuiteCRMRestClient::getInstance()->get_note_attachment($this->id);
        return $attachment['note_attachment'];
    }

    /**
     * @param $file_name
     * @param $file_location
     */
    public function addAttachment($file_name, $file_location)
    {
        $this->file_name = $file_name;
        $this->file_location = $file_location;
    }

    /**
     * @param $note_id
     * @param $file_name
     * @param $file_location
     */
    public function setNoteAttachment($note_id, $file_name, $file_location)
    {
        \SuiteCRMRestClient\SuiteCRMRestClient::getInstance()->set_note_attachment($note_id, $file_name, $file_location);
    }

    /**
     * @return array
     */
    public function getFieldsToSave()
    {
        $data = parent::getFieldsToSave();

        if (isset($this->file_name) && isset($this->file_location)) {

            $binaryFile = file_get_contents($this->file_location);
            $binaryFileEncoded = base64_encode($binaryFile);

            $data['portal_flag'] = true;
            $data['filename'] = $this->file_name;
            $data['filename_file'] = $binaryFileEncoded;
        }

        return $data;
    }
}