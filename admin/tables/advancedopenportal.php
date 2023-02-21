<?php
// No direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Table\Table;

/**
 * AdvancedOpenPortal Table class
 */
class AdvancedOpenPortalTableAdvancedOpenPortal extends Table
{
	/**
	 * Constructor
	 *
	 * @param object $db Database connector object
	 */
	public function __construct(&$db) 
	{
		parent::__construct('#__advancedopenportal', 'id', $db);
	}
}
