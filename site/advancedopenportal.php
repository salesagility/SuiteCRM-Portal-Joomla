<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;

// Get an instance of the controller
$controller = BaseController::getInstance('AdvancedOpenPortal');

// Perform the Request task
$controller->execute(Factory::getApplication()->getInput()->get('task'));
 
// Redirect if set by the controller
$controller->redirect();
