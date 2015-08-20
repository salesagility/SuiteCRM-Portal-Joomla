<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 *
 */
class advancedopenportalViewnewcase extends JViewLegacy
{
	// Overwriting JViewLegacy display method
	function display($tpl = null) 
	{
        include_once 'components/com_advancedopenportal/models/SugarCasesConnection.php';
        $user =& JFactory::getUser();
        $this->errors = array();
        $caseConnection = SugarCasesConnection::getInstance();
        require_once 'components/com_advancedopenportal/models/advancedopenportal.php';
        $settings = AdvancedOpenPortalModelAdvancedOpenPortal::getSettings();
        $this->allow_priority = $settings->allow_priority;
        if($this->allow_priority){
            $this->priorities = $caseConnection->getPriorities();
        }else{
            $this->priorities = array();
        }
        $this->allow_type = $settings->allow_type;
        if($this->allow_type) {
            $this->types = $caseConnection->getTypes();
        }else{
            $this->types = array();
        }
		// Display the view
		parent::display($tpl);
	}
}
