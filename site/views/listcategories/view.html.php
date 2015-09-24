<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * HTML View class for the HelloWorld Component
 */
class advancedopenportalViewlistcategories extends JViewLegacy
{
	// Overwriting JViewLegacy display method
	function display($tpl = null) 
	{
        include_once 'components/com_advancedopenportal/models/SugarKbConnection.php';
        $user =& JFactory::getUser();
        $this->errors = array();
        $mainframe =& JFactory::getApplication();
        $limit = $mainframe->getUserStateFromRequest( "limit", 'limit', $mainframe->getCfg('list_limit') );
        $limitstart = $mainframe->getUserStateFromRequest( "$option.limitstart", 'limitstart', 0 );

        //set limit to null if the 'All' opton is set in the pagination limit dropdown.
        if($limit == '0'){
            $limit = null;
        }

        $this->start = $limitstart;
        $this->limit = $limit;

        $Connection = SugarKbConnection::getInstance();
        $this->count = $Connection->countCategories();
        $this->categories = $Connection->getCategories($this->start,$this->limit);

		if($this->count == 0){
                JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_advancedopenportal&view=nocategories'),  JText::_('COM_ADVANCEDOPENKNOWLEDGEBASE_NO_CATEGORY'), 'error');
        }
		else{
             parent::display($tpl);
        }

	}
}
