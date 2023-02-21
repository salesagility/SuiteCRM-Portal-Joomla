<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;

include_once 'components/com_advancedopenportal/models/AdvancedOpenPortalModel.php';

/**
 * General Controller of Advanced OpenPortal component
 */
class AdvancedOpenPortalController extends AdminController
{

    /**
     * @throws Exception
     */
    public function __construct($config = [], MVCFactoryInterface $factory = null, $app = null, $input = null)
    {
        $this->name = 'AdvancedOpenPortals';
        $this->model_prefix = 'AdvancedOpenPortalModel';
        
        parent::__construct($config, $factory, $app, $input);
    }
    

    /**
     * display task
     *
     * @return void
     * @throws Exception
     */
    public function display($cachable = false, $urlparams = []): void
    {
		// set default view if not set
        $this->input->set('view', $this->app->input->getCmd('view', 'AdvancedOpenPortals'));
        
        // call parent behavior
        parent::display($cachable, $urlparams);
    }

    /**
     * @throws Exception
     */
    public function save()
    {
        // Check for request forgeries.
        $this->checkToken();
        
        $url = $this->input->get('sugar_url', '', 'trim');
        $user = $this->input->get('sugar_user', '','trim');
        $pass = $this->input->get('sugar_pass', '', 'trim');
        $reopen = $this->input->get('allow_case_reopen', false, 'bool');
        $close = $this->input->get('allow_case_closing', false, 'bool');
        $priority = $this->input->get('allow_priority', false, 'bool');
        $type = $this->input->get('allow_type', false, 'bool');
        
        /** @var AdvancedOpenPortalModelAdvancedOpenPortals $model */
        $model = $this->getModel();
        
        if ($model->storeSettings($url, $user, $pass, $reopen, $close, $priority, $type)) {
            $this->app->enqueueMessage(Text::_('COM_ADVANCEDOPENPORTAL_SETTINGS_SAVED'));
        }
        
        $this->display();
    }
    
}