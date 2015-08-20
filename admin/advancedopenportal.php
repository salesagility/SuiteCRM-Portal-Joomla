<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Set some global property
$document = JFactory::getDocument();

// import joomla controller library
jimport('joomla.application.component.controller');
 
// Get an instance of the controller prefixed by AdvancedOpenPortal
$controller = JController::getInstance('AdvancedOpenPortal');
 
// Perform the Request task
$controller->execute(JRequest::getCmd('task'));
 
// Redirect if set by the controller
$controller->redirect();
