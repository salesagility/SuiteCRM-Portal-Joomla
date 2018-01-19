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
    $this->priorities = SugarCasesConnection::getPriorities();
    $this->allow_priority = SugarCasesConnection::isAllowedPriority();
    $this->types = SugarCasesConnection::getTypes();
    $this->allow_type = SugarCasesConnection::isAllowedType();

    // Display the view
    parent::display($tpl);
}
}
