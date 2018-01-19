<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');

include_once 'components/com_advancedopenportal/models/SugarCase.php';
/**
 * HTML View class for the HelloWorld Component
 */
class advancedopenportalViewshowcase extends JViewLegacy
{
	// Overwriting JViewLegacy display method
	function display($tpl = null) 
	{
        $this->allow_case_reopen = SugarCasesConnection::isAllowedReopening();
        $this->allow_case_closing = SugarCasesConnection::isAllowedClosing();

        $this->case = SugarCase::fromID(JRequest::getVar('id'));
        $this->case->loadDisplayData();
        if(!$this->case){
            JFactory::getApplication()->redirect(JURI::base()."?option=com_advancedopenportal");
        }
		parent::display($tpl);
	}
}
