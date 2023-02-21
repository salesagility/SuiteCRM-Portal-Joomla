<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Uri\Uri;


class advancedopenportalViewshowcase extends HtmlView
{
	// Overwriting JViewLegacy display method
	function display($tpl = null) 
	{
        include_once 'components/com_advancedopenportal/models/SugarCasesConnection.php';
        $user = $this->getCurrentUser();
        $this->errors = array();
        $case_id = Factory::getApplication()->getInput()->get('id');
        $caseConnection = SugarCasesConnection::getInstance();

        require_once 'components/com_advancedopenportal/models/AdvancedOpenPortalModel.php';
        $settings = AdvancedOpenPortalModelAdvancedOpenPortal::getSettings();
        $this->allow_case_reopen = $settings->allow_case_reopen;
        $this->allow_case_closing = $settings->allow_case_closing;
        $this->case = $caseConnection->getCase($case_id,$user->getParam("sugarid"));
        if(!$this->case){
            Factory::getApplication()->redirect(URI::base()."?option=com_advancedopenportal");
        }
		parent::display($tpl);
	}
}
