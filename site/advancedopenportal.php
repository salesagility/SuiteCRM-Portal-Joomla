<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import joomla controller library
jimport('joomla.application.component.controller');

require_once __DIR__ . '/vendor/suitecrm/restclient/src/SuiteCRMRestClient.php';
require_once __DIR__ . '/JoomlaAdapter.php';
\SuiteCRMRestClient\SuiteCRMRestClient::init(new \SuiteCRMRestClient\Adapters\JoomlaAdapter());
 
// Get an instance of the controller prefixed by SaglityPortal
$controller = JControllerLegacy::getInstance('AdvancedOpenPortal');
 
// Perform the Request task
$controller->execute(JRequest::getCmd('task'));
 
// Redirect if set by the controller
$controller->redirect();
