<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 *
 */
class advancedopenportalViewlistcases extends JViewLegacy
{
    // Overwriting JViewLegacy display method
    function display($tpl = null)
    {
        $this->contact = SugarCasesConnection::currentSugarContact();
        $this->cases = $this->contact->getCases();
        $this->states = SugarCasesConnection::getStates();
        $this->validPortalUser = SugarCasesConnection::currentUserIsValidPortalUser();
        $this->userBlocked = SugarCasesConnection::currentUserIsBlocked();

        parent::display($tpl);
    }
}