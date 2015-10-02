<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HTML View class for the HelloWorld Component
 */
class advancedopenportalViewshowarticle extends JViewLegacy
{
    // Overwriting JViewLegacy display method
    function display($tpl = null)
    {
        include_once 'components/com_advancedopenportal/models/SugarKbConnection.php';
        $user =& JFactory::getUser();
        $this->errors = array();
        $id = JRequest::getVar('id');

        $Connection = SugarKbConnection::getInstance();
        $this->ann = $Connection->getArticle($id);

        foreach($this->ann['entry_list'] as $fel_mod){
            $this->article = $fel_mod['name_value_list'];
        }

        foreach($this->ann['relationship_list'] as $rel_mod){
            $this->cat = $rel_mod[0]['records'];
        }

        // Display the view
        parent::display($tpl);
    }
}
