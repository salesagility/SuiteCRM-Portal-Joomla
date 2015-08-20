<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla modelitem library
jimport('joomla.application.component.modelitem');
 
/**
 * AdvancedOpenPortal Model
 */
class AdvancedOpenPortalModelAdvancedOpenPortal extends JModelItem
{
	/**
	 * @var string msg
	 */
	protected $msg;

    public static function getSettings(){
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select(array('*'));
        $query->from('#__advancedopenportal');
        $db->setQuery($query);
        $list = $db->loadObjectList();
        if(count($list)>0){
            return $list[0];
        }
        return array();
    }

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	1.6
	 */
	public function getTable($type = 'AdvancedOpenPortal', $prefix = 'AdvancedOpenPortalTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	/**
	 * Get the message
	 * @return string The message to be displayed to the user
	 */
	public function getMsg()
	{
		if (!isset($this->msg))
		{
			$id = JRequest::getInt('id');
			// Get a TableHelloWorld instance
			$table = $this->getTable();
 
			// Load the message
			$table->load($id);
 
			// Assign the message
			$this->msg = $table->sugar_user;
		}
		return $this->msg;
	}
}
