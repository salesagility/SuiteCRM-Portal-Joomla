<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView;

class advancedopenportalViewattachment extends HtmlView
{
    // Overwriting JViewLegacy display method
    function display($tpl = null)
    {
        include_once 'components/com_advancedopenportal/models/SugarCasesConnection.php';
        $user = Factory::getUser();
        $this->errors = array();
        $note_id = Factory::getApplication()->getInput()->get('id');
        $caseConnection = new SugarCasesConnection();
        $this->attachment = $caseConnection->getNoteAttachment($note_id);
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $file = base64_decode($this->attachment['file']);
        $mime = finfo_buffer($finfo, $file);
        header("Content-type: ".$mime);
        header("Content-Disposition: attachment;filename=".$this->attachment['filename']);
        echo $file;
        jexit();
    }
}
