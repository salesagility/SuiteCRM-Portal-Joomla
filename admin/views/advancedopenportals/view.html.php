<?php
// No direct access to this file
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;

defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * Portal Admin View
 */
class AdvancedOpenPortalsViewAdvancedOpenPortals extends HtmlView
{
    /**
     * @var array|mixed
     */
    public $items;

    /**
	 * HelloWorlds view display method
	 * @return void
	 */
	public function display($tpl = null): void
    {
		// Get data from the model
		$items = $this->get('Items');
        
		// Assign data to the view
		$this->items = $items[0] ?? [];
		
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
        ToolbarHelper::title(Text::_('COM_ADVANCEDOPENPORTAL_MANAGER_ADVANCEDOPENPORTALS'));
        $toolbar = Toolbar::getInstance();
        $toolbar->apply('save');
	}

    protected function setDocument()
    {
        $document = JFactory::getDocument();
        $document->setTitle(Text::_('COM_ADVANCEDOPENPORTAL_MANAGER_ADVANCEDOPENPORTALS'));
    }
}
