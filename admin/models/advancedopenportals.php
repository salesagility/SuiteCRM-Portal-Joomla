<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
// import the Joomla modellist library
jimport('joomla.application.component.modellist');
/**
 * AdvancedOpenPortalList Model
 */
class AdvancedOpenPortalModelAdvancedOpenPortals extends JModelList
{

    public static function storeSettings($url, $user, $pass, $reopen, $close, $priority, $type){

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $ob = new stdClass();
        $ob->id = 1;
        $ob->sugar_url = $url;
        $ob->sugar_user= $user;
        $ob->allow_case_reopen = $reopen;
        $ob->allow_case_closing = $close;
        $ob->allow_priority = $priority;
        $ob->allow_type = $type;
        try {
            $db->updateObject('#__advancedopenportal',$ob,'id');
            if($db->getAffectedRows() == 0){
                $db->insertObject('#__advancedopenportal', $ob);
            }
        } catch (Exception $e) {
            return false;
        }
        //Do pass as a separate query since they may have left the md5'd pass unchanged
        //TODO: Find a nicer way to do this
        $fields = array("sugar_pass='" . md5($pass) . "'");
        $conditions = array("sugar_pass!='" . $pass . "' OR sugar_pass IS NULL");
        $query->update($db->quoteName('#__advancedopenportal'))->set($fields)->where($conditions);
        $db->setQuery($query);
        try {
            $db->query();
        } catch (Exception $e) {
            return false;
        }
        return true;
    }

	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return	string	An SQL query
	 */
	protected function getListQuery()
	{
		// Create a new query object.		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		// Select some fields
		$query->select('*');
		// From the hello table
		$query->from('#__advancedopenportal');
		return $query;
	}
}
