<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * HTML View class for the HelloWorld Component
 */
class advancedopenportalViewlistarticles extends JViewLegacy
{
	// Overwriting JViewLegacy display method
	function display($tpl = null) 
	{
        include_once 'components/com_advancedopenportal/models/SugarKbConnection.php';
        $user =& JFactory::getUser();
        $mainframe =& JFactory::getApplication();
        $limit = $mainframe->getUserStateFromRequest( "limit", 'limit', $mainframe->getCfg('list_limit') );
        $limitstart = $mainframe->getUserStateFromRequest( "$option.limitstart", 'limitstart', 0 );

        //set limit to null if the 'All' opton is set in the pagination limit dropdown.
        if($limit == '0'){
           $limit = null;
        }

        $this->errors = array();
        $this->cat_id = JRequest::getVar('id');
        $this->start = $limitstart;
        $this->limit = $limit;


        /* $contact_id = $user->getParam('sugarid');*/
        $Connection = SugarKbConnection::getInstance();
        $this->count = $Connection->categoryCount($this->cat_id);
        $this->articles = $Connection->getArticles($this->cat_id, $this->start,  $this->limit);

        // Display the view
        parent::display($tpl);
	}
}
