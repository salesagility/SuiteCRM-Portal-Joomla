<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\AdminModel;

/**
 * AdvancedOpenPortalList Model
 */
class AdvancedOpenPortalModelAdvancedOpenPortals extends AdminModel
{
    
    public function storeSettings($url, $user, $pass, $reopen, $close, $priority, $type): bool
    {
        $db = Factory::getDbo();
        
        $ob = new stdClass();
        $ob->id = 1;
        $ob->sugar_url = $url;
        $ob->sugar_user = $user;
        $ob->allow_case_reopen = $reopen ? 1 : 0;
        $ob->allow_case_closing = $close ? 1 : 0;
        $ob->allow_priority = $priority ? 1 : 0;
        $ob->allow_type = $type ? 1 : 0;
        if (!empty($pass)) {
            $ob->sugar_pass =  md5($pass);
        }
        
        try {
            $db->updateObject('#__advancedopenportal', $ob, 'id');
            if ($db->getAffectedRows() === 0) {
                $db->insertObject('#__advancedopenportal', $ob);
            }
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
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		// Select some fields
		$query->select('*');
		// From the hello table
		$query->from('#__advancedopenportal');
		return $query;
	}

    public function getItems()
    {
        $db = Factory::getDbo();
        $query = $this->getListQuery();
        try {
            $result = $db->setQuery($query)->loadObjectList();
        } catch (Exception $e) {
            echo Text::sprintf('JLIB_DATABASE_ERROR_FUNCTION_FAILED', $e->getCode(), $e->getMessage()) . '<br>';

            return [];
        }

        return $result;
    }

    public function getForm($data = [], $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm('com_advancedopenportal.source', 'source',
            ['control' => 'jform', 'load_data' => $loadData]);

        if (empty($form)) {
            return false;
        }

        return $form;
    }
}
