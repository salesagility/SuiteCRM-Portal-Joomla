<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * HelloWorlds View
 */
class AdvancedOpenPortalViewAdvancedOpenPortals extends JViewLegacy
{
	/**
	 * HelloWorlds view display method
	 * @return void
	 */
	function display($tpl = null) 
	{
		// Get data from the model
		$items = $this->get('Items');
		$pagination = $this->get('Pagination');
 
		// Check for errors.
		if (count($errors = $this->get('Errors'))) 
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}
		// Assign data to the view
		$this->items = $items[0];
		$this->pagination = $pagination;
		
		// Set the toolbar
		$this->addToolBar();
 
		// Display the template
		parent::display($tpl);

        // Set the document
        $this->setDocument();
	}
	
	/**
	 * Setting the toolbar
	 */
	protected function addToolBar() 
	{
		JToolBarHelper::title(JText::_('COM_ADVANCEDOPENPORTAL_MANAGER_ADVANCEDOPENPORTALS'));
	}

    protected function setDocument()
    {
        $document = JFactory::getDocument();
        $document->setTitle(JText::_('Advanced OpenPortal Settings'));
    }
}
