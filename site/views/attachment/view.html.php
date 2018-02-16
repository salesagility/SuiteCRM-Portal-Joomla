<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 *
 */
class advancedopenportalViewattachment extends JViewLegacy
{
    // Overwriting JViewLegacy display method
    function display($tpl = null)
    {
        $note = SugarNote::fromID(JRequest::getVar('id'));
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $file = base64_decode($note->filename_file);
        $mime = finfo_buffer($finfo, $file);
        header("Content-type: ".$mime);
        header("Content-Disposition: attachment;filename=".$note->filename);
        echo $file;
        jexit();
    }
}
