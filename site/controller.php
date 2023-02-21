<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\User\User;
use Joomla\CMS\User\UserHelper;

require_once 'components/com_advancedopenportal/models/SugarCasesConnection.php';

/**
 * Sagility Portal Component Controller
 */
class AdvancedOpenPortalController extends BaseController
{

    public function display($cachable = false, $url_params = false)
    {
        $user = $this->app->getIdentity();

        $view = $this->input->get('view');
        if ($view === 'advancedopenportal') {
            $this->input->set('view', 'listcases');
            $view = "listcases";
        }
        if (SugarCasesConnection::isValidPortalUser($user) && !SugarCasesConnection::isUserBlocked($user)) {
            parent::display($cachable, $url_params);
        } else {
            if (!$user->id) {
                $msg = Text::_('COM_ADVANCEDOPENPORTAL_LOGIN_REQUIRED');
            } elseif (SugarCasesConnection::isUserBlocked($user)) {
                $msg = Text::_('COM_ADVANCEDOPENPORTAL_PORTAL_USER_BLOCKED');
            } else {
                $msg = Text::_('COM_ADVANCEDOPENPORTAL_NO_PORTAL_ACCOUNT');
            }
            if ($view !== 'listcases') {
                Factory::getApplication()->enqueueMessage($msg, 'error');
                Factory::getApplication()->redirect(URI::base());
            } else {
                Factory::getApplication()->enqueueMessage($msg, 'error');
                parent::display($cachable, $url_params);
            }
        }
    }

    private function getToggletatus($status)
    {
        if (strpos($status, 'Closed') === 0) {
            return "Open_New";
        }

        if (strpos($status, 'Open') === 0) {
            return "Closed_Closed";
        }

        return null;
    }

    public function toggleCaseStatus(){
        $con = SugarCasesConnection::getInstance();
        require_once 'components/com_advancedopenportal/models/AdvancedOpenPortalModel.php';
        $settings = AdvancedOpenPortalModelAdvancedOpenPortal::getSettings();

        $newStatus = $this->getToggletatus($_REQUEST['case_status']);
        if(($newStatus === 'Open_New' && !$settings->allow_case_reopen) || ($newStatus === 'Closed_Closed' && !$settings->allow_case_closing)){
            Factory::getApplication()->redirect(URI::base()."?option=com_advancedopenportal&view=showcase&id=".$_REQUEST['case_id']);
            return;
        }
        $user = $this->app->getIdentity();
        $case = $con->getCase($_REQUEST['case_id'],$user->getParam("sugarid"));
        if(!$case){
            Factory::getApplication()->redirect(URI::base()."?option=com_advancedopenportal");
            return;
        }

        $con->setCaseStatus($_REQUEST['case_id'],$newStatus);
        Factory::getApplication()->redirect(URI::base()."?option=com_advancedopenportal&view=showcase&id=".$_REQUEST['case_id']);
    }


    function newcase(){
        $errors = [];
        $subject = Factory::getApplication()->getInput()->get("subject", null, 'string');
        $description = Factory::getApplication()->getInput()->get("description", null, 'default', 'html', 4);
        $type = Factory::getApplication()->getInput()->get("type");
        $priority = Factory::getApplication()->getInput()->get("priority");
        $file_count = Factory::getApplication()->getInput()->get("file_count");
        $files = [];
        for($count = 1; $count <= $file_count; $count++){
            if(!array_key_exists("file".$count,$_FILES)){
                continue;
            }
            $fileError = $_FILES["file".$count]['error'];
            if ($fileError > 0){
                switch ($fileError){
                    case 1:
                    case 2:
                        $errors["file".$count] = "File too large";
                        break;
                    case 3:
                        $errors["file".$count] = "Partial upload";
                        break;
                }
                continue;
            }
            $files[$_FILES["file".$count]['name']] = $_FILES["file".$count]['tmp_name'];
        }
        $user = $this->app->getIdentity();
        $contact_id = $user->getParam("sugarid");
        $casesConnection = SugarCasesConnection::getInstance();
        $new_case = $casesConnection->newCase($contact_id, $subject, $description, $type, $priority, $files);
        Factory::getApplication()->redirect(URI::base()."?option=com_advancedopenportal&view=showcase&id=".$new_case);
    }

    function addupdate(){
        $case_id = Factory::getApplication()->getInput()->get("case_id");
        $description = Factory::getApplication()->getInput()->get("update_text",null, 'default', 'html',4);
        if(!$case_id){
            echo json_encode(array('Case Id is required'));
            return;
        }
        if(!$description){
            echo json_encode(array('Update Text is required'));
            return;
        }
        $user = $this->app->getIdentity();
        $contact_id = $user->getParam("sugarid");
        $casesConnection = SugarCasesConnection::getInstance();
        $case_update = $casesConnection->postUpdate($case_id,$description,$contact_id);
        $file_count = Factory::getApplication()->getInput()->get("file_count");

        if($file_count){
            $case_update->notes = [];
            $files = [];
            for($count = 1; $count <= $file_count; $count++){
                if(!array_key_exists("file".$count,$_FILES)){
                    continue;
                }
                $fileError = $_FILES["file".$count]['error'];
                if ($fileError > 0){
                    switch ($fileError){
                        case 1:
                        case 2:
                            $errors["file".$count] = "File too large";
                            break;
                        case 3:
                            $errors["file".$count] = "Partial upload";
                            break;
                    }
                    continue;
                }
                $files[$_FILES["file".$count]['name']] = $_FILES["file".$count]['tmp_name'];
            }
            $response = $casesConnection->addFiles($case_id, $case_update->id, $contact_id, $files);
            foreach($response as $res){
                $case_update->notes[] = $res;
            }
        }


        echo json_encode($case_update);
    }

    function create() {
        // Get the document object.
        $document = Factory::getDocument();

        // Set the MIME type for JSON output.
        $document->setMimeEncoding('application/json');
        //Connect to Sugar via Rest interface
        include_once 'components/com_advancedopenportal/sugarRestClient.php';
        $restClient = new sugarRestClient();
        $restClient->login();

        if(isset($_REQUEST['sug']) && $_REQUEST['sug'] != ''){

            $contacts = $restClient->getEntry('Contacts',$_REQUEST['sug'],array('name','email1'));
            if(!empty($contacts['entry_list'])){

                $contact = $contacts['entry_list'][0]['name_value_list'];

                $pass = UserHelper::genRandomPassword(16);

                $data = [];
                $data['fullname'] = $contact['name']['value'];
                $data['email'] = $contact['email1']['value'];
                $data['password'] = UserHelper::hashPassword($pass);
                $data['username'] = $contact['email1']['value'];
                
                $params = ComponentHelper::getParams('com_users');
                
                // Default to Registered.
                $defaultUserGroup = $params->get('new_usertype', 2);

                $user = User::getInstance();
                
                $user->set('id', 0);
                $user->set('name', $data['fullname']);
                $user->set('username', $data['username']);
                $user->set('password', $data['password']);
                $user->set('email', $data['email']);  // Result should contain an email (check)
                $user->set('usertype', 'deprecated');
                $user->set('groups', array($defaultUserGroup));
                $user->setParam('sugarid', $_REQUEST['sug']);

                //If autoregister is set let's register the user
                $autoregister = $options['autoregister'] ?? $params->get('autoregister', 1);

                if ($autoregister) {
                    if (!$user->save()) {
                        echo json_encode(array("error"=>"Failed to save user ".implode(" ",$user->getErrors())));
                        Factory::getApplication()->close();
                        return JError::raiseWarning('SOME_ERROR_CODE', $user->getError());
                    }
                }
                else {
                    // No existing user and autoregister off, this is a temporary user.
                    $user->set('tmp_user', true);
                }

                $restClient->setEntry('Contacts',array('id'=>$_REQUEST['sug'], 'joomla_account_id' => $user->id,'joomla_account_access' => $pass, ));
                echo json_encode(array("success"=>true));
            }
        }else{
            echo json_encode(array("error"=>"ID Not specified"));
        }
        Factory::getApplication()->close();
    }

    function update_e(){
        //Connect to Sugar via Rest interface
        include_once 'components/com_advancedopenportal/sugarRestClient.php';
        $restClient = new sugarRestClient();
        $restClient->login();

        if(isset($_REQUEST['sug']) && $_REQUEST['sug'] != ''){

            $contacts = $restClient->getEntry('Contacts',$_REQUEST['sug'],array('name','email1','joomla_account_id'));

            if(!empty($contacts['entry_list'])){
                $contact = $contacts['entry_list'][0]['name_value_list'];

                $userId = (int) $_REQUEST['uid'];

                // Check for a valid user id.
                if (!$userId) {
                    $this->setError(Text::_('COM_USERS_ACTIVATION_TOKEN_NOT_FOUND'));
                    return false;
                }

                // Load the users plugin group.
                JPluginHelper::importPlugin('user');

                // Activate the user.
                $user = Factory::getUser($userId);

                $user->set('name', $contact['name']['value']);
                $user->set('email', $contact['email1']['value']);
                $user->set('username', $contact['email1']['value']);

                $user->save();
            }
        }

    }

    function disable_user(){
        $this->setUserDisabled(1);
    }

    function enable_user(){
        $this->setUserDisabled(0);
    }

    private function setUserDisabled($disable){
        if(isset($_REQUEST['sug']) && $_REQUEST['sug'] != ''){
            include_once 'components/com_advancedopenportal/sugarRestClient.php';
            $restClient = new sugarRestClient();
            $restClient->login();
            $contacts = $restClient->getEntry('Contacts',$_REQUEST['sug'],array('joomla_account_id'));
            if(!empty($contacts['entry_list'])){
                $contact = $contacts['entry_list'][0]['name_value_list'];
                $userId = (int) $_REQUEST['uid'];
                if (!$userId) {
                    echo json_encode(array("error"=>Text::_('COM_USERS_ACTIVATION_TOKEN_NOT_FOUND')));

                }else{
                    JPluginHelper::importPlugin('user');
                    $user = Factory::getUser($userId);
                    $user->setParam('aop_block', $disable);
                    $user->save();
                    echo json_encode(array("success"=>true));
                }
            }
        }
        Factory::getApplication()->close();
    }


}
