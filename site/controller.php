<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla controller library
jimport('joomla.application.component.controller');
require_once 'components/com_advancedopenportal/models/SugarCasesConnection.php';
require_once 'components/com_advancedopenportal/models/SugarCase.php';
require_once 'components/com_advancedopenportal/models/SugarUpdate.php';
/**
 * Sagility Portal Component Controller
 */
class AdvancedOpenPortalController extends JControllerLegacy{

    /**
     * @param bool $cachable
     * @param bool $url_params
     */
    public function display($cachable = false, $url_params = false){
        $user =& JFactory::getUser();
        $view = JRequest::getVar("view");
        if($view == 'advancedopenportal'){
            JRequest::setVar("view","listcases");
            $view = "listcases";
        }
        if(SugarCasesConnection::currentUserIsValidPortalUser() && !SugarCasesConnection::currentUserIsBlocked()){
            parent::display($cachable,$url_params);
        }else{
            if(!$user->id){
                $msg = JText::_('COM_ADVANCEDOPENPORTAL_LOGIN_REQUIRED');
            }elseif(SugarCasesConnection::currentUserIsBlocked()){
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

    /**
     * Opens or closes a case
     */
    public function toggleCaseStatus(){
        $case = SugarCase::fromID($_REQUEST['case_id']);

        if(!$case){
            JFactory::getApplication()->redirect(JURI::base()."?option=com_advancedopenportal");
            return;
        }

        if($case->toggleState($_REQUEST['oldState'])){
            $case->save();
        }

        JFactory::getApplication()->redirect(JURI::base()."?option=com_advancedopenportal&view=showcase&id=".$_REQUEST['case_id']);
    }


    /**
     *  Creates a new Case
     */
    function newcase(){
        $contact = SugarCasesConnection::currentSugarContact();

        $case = SugarCase::fromScratch(array(
            'name' => JRequest::getVar("subject"),
            'description' => JRequest::getVar("description",null, 'default', 'html',4),
            'type' => JRequest::getVar("type"),
            'priority' => JRequest::getVar("priority"),
            'account_name' => '',
            "status" => 'New',
            "state" => 'Open',
            'update_date_entered' => true,
            "contact_id" => $contact->id,
            "contact_created_by_id" => $contact->id,
        ));
        $case = $case->save();
        $case->addRelationship("contacts", $contact->id);
        $case->addRelationship("accounts", $contact->account_id);
        $case-> addFiles($this->getUploadedFiles(JRequest::getVar("file_count")));

        JFactory::getApplication()->redirect(JURI::base()."?option=com_advancedopenportal&view=showcase&id=".$case->id);
    }

    /**
     *  Creates a Case Update
     * @return JSON
     */
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
        $contact_id = SugarCasesConnection::currentSugarContactId();
        $caseUpdate = SugarUpdate::fromScratch(array(
            'name' => $description,
            'description' => $description,
            'contact_id' => $contact_id,
            'case_id' => $case_id
        ))->save();
        $caseUpdate->loadContact();

        $caseUpdate-> addFiles($this->getUploadedFiles(JRequest::getVar("file_count")));
        $caseUpdate->loadDisplayData();

        echo $caseUpdate->toJson();
    }

    /**
     * Creates a new user based on information sent from the SuiteCRM Contacts Module
     *
     * @return JException
     */
    function create() {
        // Get the document object.
        $document =& JFactory::getDocument();

        // Set the MIME type for JSON output.
        $document->setMimeEncoding('application/json');

        if(isset($_REQUEST['sug']) && $_REQUEST['sug'] != ''){

            $contacts = SugarRestClient::getInstance()->getEntry('Contacts',$_REQUEST['sug'],array('name','email1'));

            if(!empty($contacts['data'])){

                $contact = $contacts['data']['attributes'];

                $pass = JUserHelper::genRandomPassword();
                $pass_c = JUserHelper::getCryptedPassword($pass);

                $user = JUser::getInstance();

                jimport('joomla.application.component.helper');

                $config = JFactory::getConfig();
                $params = JComponentHelper::getParams('com_users');
                // Default to Registered.
                $defaultUserGroup = $params->get('new_usertype', 2);

                $acl = JFactory::getACL();

                $user->set('id'         , 0);
                $user->set('name'           , $contact['name']);
                $user->set('username'       , $contact['email1']);
                $user->set('password'       , $pass_c);
                $user->set('email'          , $contact['email1']);  // Result should contain an email (check)
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

                SugarContact::fromScratch(array(
                    'id' => $_REQUEST['sug'],
                    'last_name' => $contact['last_name'],
                    'joomla_account_id' => $user->id,
                    'joomla_account_access' => $pass,
                ))->save();

                echo json_encode(array("success"=>true));
            }
        }else{
            echo json_encode(array("error"=>"ID Not specified"));
        }
        JFactory::getApplication()->close();
    }

    function disable_user(){
        $this->setUserDisabled(1);
    }

    function enable_user(){
        $this->setUserDisabled(0);
    }

    /**
     * Disables or enables a user, is called from the SuiteCRM Contacts Module
     *
     * @param $disable
     */
    private function setUserDisabled($disable){
        if(isset($_REQUEST['sug']) && $_REQUEST['sug'] != ''){
            $contact = SugarContact::fromID($_REQUEST['sug']);
            if($contact){
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

    /**
     * Returns an array of uploaded filenames and locations
     *
     * @param $file_count
     * @return array
     */
    private function getUploadedFiles($file_count)
    {
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
        return $files;
    }

}
