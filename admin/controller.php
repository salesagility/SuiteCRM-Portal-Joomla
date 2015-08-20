<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla controller library
jimport('joomla.application.component.controller');
include_once 'components/com_advancedopenportal/models/advancedopenportals.php';
/**
 * General Controller of Advanced OpenPortal component
 */
class AdvancedOpenPortalController extends JController
{
	/**
	 * display task
	 *
	 * @return void
	 */
	function display($cachable = false) 
	{
        if(array_key_exists('submit',$_REQUEST)){
            $url = $_REQUEST['sugar_url'];
            $user = $_REQUEST['sugar_user'];
            $pass = $_REQUEST['sugar_pass'];
            $reopen = !empty($_REQUEST['allow_case_reopen']);
            $close = !empty($_REQUEST['allow_case_closing']);
            $priority = !empty($_REQUEST['allow_priority']);
            $type = !empty($_REQUEST['allow_type']);
            AdvancedOpenPortalModelAdvancedOpenPortals::storeSettings($url,$user,$pass, $reopen, $close, $priority, $type);
            JFactory::getApplication()->enqueueMessage(JText::_('COM_ADVANCEDOPENPORTAL_SETTINGS_SAVED'));

        }
		// set default view if not set
		JRequest::setVar('view', JRequest::getCmd('view', 'AdvancedOpenPortals'));
 
		// call parent behavior
		parent::display($cachable);
	}
}
