<?php
// No direct access to this file
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ItemModel;
use Joomla\CMS\Table\Table;

defined('_JEXEC') or die('Restricted access');
 
/**
 * AdvancedOpenPortal Model
 */
class AdvancedOpenPortalModelAdvancedOpenPortal extends ItemModel
{
	/**
	 * @var string msg
	 */
	protected $msg;

    public static function getSettings()
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select(array('*'));
        $query->from('#__advancedopenportal');
        $db->setQuery($query);
        $list = $db->loadObjectList();
        if (count($list) > 0) {
            return $list[0];
        }

        return [];
    }

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type $type The table type to instantiate
	 * @param	string $prefix A prefix for the table class name. Optional.
	 * @param	array $config Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	1.6
	 */
	public function getTable($type = 'AdvancedOpenPortal', $prefix = 'AdvancedOpenPortalTable', $config = array())
	{
		return Table::getInstance($type, $prefix, $config);
	}
	/**
	 * Get the message
	 * @return string The message to be displayed to the user
	 */
	public function getMsg()
	{
		if (!isset($this->msg))
		{
			$id = Factory::getApplication()->getInput()->get('id',null,'int');
            // Get a TableHelloWorld instance
			$table = $this->getTable();
 
			// Load the message
			$table->load($id);
 
			// Assign the message
			$this->msg = $table->sugar_user;
		}
		return $this->msg;
	}

    public function getItem($pk = null)
    {
        // TODO: Implement getItem() method.
    }
}
