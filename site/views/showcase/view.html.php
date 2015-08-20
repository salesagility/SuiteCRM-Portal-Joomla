<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * HTML View class for the HelloWorld Component
 */
class advancedopenportalViewshowcase extends JViewLegacy
{
	// Overwriting JViewLegacy display method
	function display($tpl = null) 
	{
        include_once 'components/com_advancedopenportal/models/SugarCasesConnection.php';
        $user =& JFactory::getUser();
        $this->errors = array();
        $case_id = JRequest::getVar('id');
        $caseConnection = SugarCasesConnection::getInstance();

        require_once 'components/com_advancedopenportal/models/advancedopenportal.php';
        $settings = AdvancedOpenPortalModelAdvancedOpenPortal::getSettings();
        $this->allow_case_reopen = $settings->allow_case_reopen;
        $this->allow_case_closing = $settings->allow_case_closing;
        $this->case = $caseConnection->getCase($case_id,$user->getParam("sugarid"));
        if(!$this->case){
            JFactory::getApplication()->redirect(JURI::base()."?option=com_advancedopenportal");
        }
		parent::display($tpl);
	}
}
