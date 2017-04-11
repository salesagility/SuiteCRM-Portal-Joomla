<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla controller library
jimport('joomla.application.component.controller');
require_once 'components/com_advancedopenportal/models/SugarCasesConnection.php';
/**
 * Sagility Portal Component Controller
 */
class AdvancedOpenPortalController extends JControllerLegacy{

    public function display($cachable = false,$url_params = false){
        $user =& JFactory::getUser();
        $view = JRequest::getVar("view");
        if($view == 'advancedopenportal'){
            JRequest::setVar("view","listcases");
            $view = "listcases";
        }
        if(SugarCasesConnection::isValidPortalUser($user) && !SugarCasesConnection::isUserBlocked($user)){
            parent::display($cachable,$url_params);
        }else{
            if(!$user->id){
                $msg = JText::_('COM_ADVANCEDOPENPORTAL_LOGIN_REQUIRED');
            }elseif(SugarCasesConnection::isUserBlocked($user)){
                $msg = JText::_('COM_ADVANCEDOPENPORTAL_PORTAL_USER_BLOCKED');
            }else{
                $msg = JText::_('COM_ADVANCEDOPENPORTAL_NO_PORTAL_ACCOUNT');
            }
            if($view != 'listcases'){
                JFactory::getApplication()->redirect(JURI::base(), $msg, 'error');
            }else{
                JFactory::getApplication()->enqueueMessage($msg, 'error');
                parent::display($cachable,$url_params);
            }
        }
    }

    private function getToggletatus($status){
        if(strpos($status, 'Closed') === 0){
            return "Open_New";
        }elseif(strpos($status, 'Open') === 0){
            return "Closed_Closed";
        }
        return null;
    }

    public function toggleCaseStatus(){
        $con = SugarCasesConnection::getInstance();
        require_once 'components/com_advancedopenportal/models/advancedopenportal.php';
        $settings = AdvancedOpenPortalModelAdvancedOpenPortal::getSettings();
        $settings->allow_case_reopen;
        $settings->allow_case_closing;

        $newStatus = $this->getToggletatus($_REQUEST['case_status']);
        if(($newStatus == 'Open_New' && !$settings->allow_case_reopen) || $newStatus == 'Closed_Closed' && !$settings->allow_case_closing){
            JFactory::getApplication()->redirect(JURI::base()."?option=com_advancedopenportal&view=showcase&id=".$_REQUEST['case_id']);
            return;
        }
        $user =& JFactory::getUser();
        $case = $con->getCase($_REQUEST['case_id'],$user->getParam("sugarid"));
        if(!$case){
            JFactory::getApplication()->redirect(JURI::base()."?option=com_advancedopenportal");
            return;
        }

        $con->setCaseStatus($_REQUEST['case_id'],$newStatus);
        JFactory::getApplication()->redirect(JURI::base()."?option=com_advancedopenportal&view=showcase&id=".$_REQUEST['case_id']);
    }


    function newcase(){
        $errors = array();
        $subject = JRequest::getVar("subject");
        $description = JRequest::getVar("description",null, 'default', 'html',4);
        $type = JRequest::getVar("type");
        $priority = JRequest::getVar("priority");
        $file_count = JRequest::getVar("file_count");
        $files = array();
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
        $user = JFactory::getUser();
        $contact_id = $user->getParam("sugarid");
        $casesConnection = SugarCasesConnection::getInstance();
        $new_case = $casesConnection->newCase($contact_id, $subject, $description, $type, $priority, $files);
        JFactory::getApplication()->redirect(JURI::base()."?option=com_advancedopenportal&view=showcase&id=".$new_case);
    }

    function addupdate(){
        $case_id = JRequest::getVar("case_id");
        $description = JRequest::getVar("update_text",null, 'default', 'html',4);
        if(!$case_id){
            echo json_encode(array('Case Id is required'));
            return;
        }
        if(!$description){
            echo json_encode(array('Update Text is required'));
            return;
        }
        $user = JFactory::getUser();
        $contact_id = $user->getParam("sugarid");
        $casesConnection = SugarCasesConnection::getInstance();
        $case_update = $casesConnection->postUpdate($case_id,$description,$contact_id);
        $file_count = JRequest::getVar("file_count");

        if($file_count){
            $case_update->notes = array();
            $files = array();
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
        $document =& JFactory::getDocument();

        // Set the MIME type for JSON output.
        $document->setMimeEncoding('application/json');
        //Connect to Sugar via Rest interface
        include_once 'components/com_advancedopenportal/sugarRestClient.php';
        $restClient = new sugarRestClient();
        $restClient->login();

        if(isset($_REQUEST['sug']) && $_REQUEST['sug'] != ''){

            $module = $this->getRequestModule();

            $sug = $this->getRequestSugarId();

            $contacts = $restClient->getEntry($module, $sug,array('name','email1'));
            if(!empty($contacts['entry_list'])){

                $contact = $contacts['entry_list'][0]['name_value_list'];

                $pass = JUserHelper::genRandomPassword();
                $pass_c = JUserHelper::getCryptedPassword($pass);

                $data = array();
                $data['fullname'] = $contact['name']['value'];
                $data['email'] = $contact['email1']['value'];
                $data['password'] = $pass_c;
                $data['username'] = $contact['email1']['value'];

                $user = JUser::getInstance();

                jimport('joomla.application.component.helper');

                $config = JFactory::getConfig();
                $params = JComponentHelper::getParams('com_users');
                // Default to Registered.
                $defaultUserGroup = $params->get('new_usertype', 2);

                $acl = JFactory::getACL();

                $user->set('id'         , 0);
                $user->set('name'           , $data['fullname']);
                $user->set('username'       , $data['username']);
                $user->set('password'       , $data['password']);
                $user->set('email'          , $data['email']);  // Result should contain an email (check)
                $user->set('usertype'       , 'deprecated');
                $user->set('groups'     , array($defaultUserGroup));
                $user->setParam('sugarid', $_REQUEST['sug']);

                //If autoregister is set let's register the user
                $autoregister = isset($options['autoregister']) ? $options['autoregister'] :  $params->get('autoregister', 1);

                if ($autoregister) {
                    if (!$user->save()) {
                        echo json_encode(array("error"=>"Failed to save user ".implode(" ",$user->getErrors())));
                        JFactory::getApplication()->close();
                        return JError::raiseWarning('SOME_ERROR_CODE', $user->getError());
                    }
                }
                else {
                    // No existing user and autoregister off, this is a temporary user.
                    $user->set('tmp_user', true);
                }

                $restClient->setEntry($module, array('id'=>$sug, 'joomla_account_id' => $user->id,'joomla_account_access' => $pass, ));
                echo json_encode(array("success"=>true));
            }
        }else{
            echo json_encode(array("error"=>"ID Not specified"));
        }
        JFactory::getApplication()->close();
    }

    function update_e(){
        //Connect to Sugar via Rest interface
        include_once 'components/com_advancedopenportal/sugarRestClient.php';
        $restClient = new sugarRestClient();
        $restClient->login();

        if(isset($_REQUEST['sug']) && $_REQUEST['sug'] != ''){

            $module = $this->getRequestModule();

            $sug = $this->getRequestSugarId();

            $contacts = $restClient->getEntry($module, $sug,array('name','email1','joomla_account_id'));

            if(!empty($contacts['entry_list'])){
                $contact = $contacts['entry_list'][0]['name_value_list'];

                $userId = (int) $_REQUEST['uid'];

                // Check for a valid user id.
                if (!$userId) {
                    $this->setError(JText::_('COM_USERS_ACTIVATION_TOKEN_NOT_FOUND'));
                    return false;
                }

                // Load the users plugin group.
                JPluginHelper::importPlugin('user');

                // Activate the user.
                $user = JFactory::getUser($userId);

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

            $module = $this->getRequestModule();

            $sug = $this->getRequestSugarId();

            $contacts = $restClient->getEntry($module, $sug,array('joomla_account_id'));
            if(!empty($contacts['entry_list'])){
                $contact = $contacts['entry_list'][0]['name_value_list'];
                $userId = (int) $_REQUEST['uid'];
                if (!$userId) {
                    echo json_encode(array("error"=>JText::_('COM_USERS_ACTIVATION_TOKEN_NOT_FOUND')));

                }else{
                    JPluginHelper::importPlugin('user');
                    $user = JFactory::getUser($userId);
                    $user->setParam('aop_block', $disable);
                    $user->save();
                    echo json_encode(array("success"=>true));
                }
            }
        }
        JFactory::getApplication()->close();
    }

    private function getRequestModule() {
        $module = 'Contacts';
        if(isset($_REQUEST['m'])) {
            $module = $_REQUEST['m'];
        }
        return $module;
    }

    private function getRequestSugarId() {
        $split = explode('::', $_REQUEST['sug']);
        $sug = $split[0];
        return $sug;
    }

}
