<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;

// Set some global property
$document = Factory::getDocument();

// Get an instance of the controller prefixed by AdvancedOpenPortal
$controller = BaseController::getInstance('AdvancedOpenPortal');

// Perform the Request task
$controller->execute(Factory::getApplication()->getInput()->get('task'));

// Redirect if set by the controller
$controller->redirect();
