<?php
/**
 *
 * @package Advanced OpenPortal
 * @copyright SalesAgility Ltd http://www.salesagility.com
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU AFFERO GENERAL PUBLIC LICENSE
 * along with this program; if not, see http://www.gnu.org/licenses
 * or write to the Free Software Foundation,Inc., 51 Franklin Street,
 * Fifth Floor, Boston, MA 02110-1301  USA
 *
 * @author Salesagility Ltd <support@salesagility.com>
 */
include_once 'components/com_advancedopenportal/sugarRestClient.php';
include_once 'components/com_advancedopenportal/models/SugarCase.php';
include_once 'components/com_advancedopenportal/models/SugarUpdate.php';
//include_once 'components/com_advancedopenportal/models/SugarAccount.php';

class SugarCasesConnection {

    private $case_fields = array('id','name','date_entered','date_modified','description','case_number','type','status','state','priority','contact_created_by_id', 'contact_created_by_name');
    private $case_update_fields = array('id','name','date_entered','date_modified','description','contact','contact_id', 'internal', 'assigned_user_id');
    private $contact_fields = array('id','first_name','last_name','date_entered','date_modified','description','portal_user_type','account_id');
    private $user_fields = array('id','first_name', 'last_name', 'date_entered','date_modified','description');
    private $note_fields = array('id','name', 'date_entered','date_modified','description','filename','file_url');
    private $account_fields = array('id','parent_id');
    
    private static $singleton;

    public function __construct() {

        $this->restClient = new sugarRestClient();
        $this->cache = & JFactory::getCache();
        $this->cache->setCaching( 1 );
        if(!$this->restClient->login()){
            throw new Exception("Failed to connect to sugar. Please check your settings.");
        }
    }

    public static function getInstance(){
        if(!self::$singleton){
            self::$singleton = new SugarCasesConnection();
        }
        return self::$singleton;
    }

    private function getCaseFields(){
        return $this->cache->call(array($this->restClient,'getAllModuleFields'),'Cases');
    }

    public function getTypes(){
        $fields = $this->getCaseFields();
        return $fields['type'];
    }

    public function getPriorities(){
        $fields = $this->getCaseFields();
        return $fields['priority'];
    }

    public function getStatuses(){
        $fields = $this->getCaseFields();
        return $fields['status'];
    }

    public function getStates(){
        $fields = $this->getCaseFields();
        return $fields['state'];
    }

    public function getCaseStatusDisplay($status){
        $statuses = $this->getStatuses();
        foreach($statuses['options'] as $option){
            if($option['name'] == $status){
                return $option['value'];
            }
        }
        return $status;
    }

    public function addFiles($caseId, $caseUpdateId, $contactId, $files){
        $results = array();
        //For every file, create a new note. Add an attachment and link the note to the
        foreach($files as $file_name => $file_location){
            $note_data = array(
                'name' => "Case Attachment: $file_name",
                'parent_id' => $caseUpdateId,
                'parent_type' => 'AOP_Case_Updates',
            );
            $new_note = $this->restClient->setEntry('Notes',$note_data);
            $note_id = $new_note['id'];
            $this->restClient->set_note_attachment($note_id, $file_name, $file_location);
            $this->restClient->setRelationship("Notes",$note_id,"contact",$contactId);
            $results[] = array('id'=>$note_id,'file_name'=>$file_name);
        }
        return $results;
    }

    public function newCase($contact_id,$subject, $description,$type,$priority,$files){

        $data = array("contact_id"=>$contact_id,
                        "contact_created_by_id"=>$contact_id,
                        "name" => $subject,
                        "status" => 'New',
                        "description" => $description,
                        "type" => $type,
                        "priority" => $priority,
                        'update_date_entered' => true,
                    );
        //TODO: Check call results
        //Create the actual case.
        $response = $this->restClient->setEntry('Cases',$data);
        $this->restClient->setRelationship("Cases",$response['id'],"contacts",$contact_id);
        //For every file, create a new note. Add an attachment and link the note to the
        foreach($files as $file_name => $file_location){
            $note_data = array(
                'name' => "Case Attachment: $file_name",
            );
            $new_note = $this->restClient->setEntry('Notes',$note_data);
            $note_id = $new_note['id'];
            $this->restClient->set_note_attachment($note_id, $file_name, $file_location);
            $this->restClient->setRelationship("Notes",$note_id,"cases",$response['id']);
            $this->restClient->setRelationship("Notes",$note_id,"contact",$contact_id);
        }
        return $response['id'];
    }

    public function postUpdate($case_id,$update_text, $contact_id){
        $data = array();
        //TODO: Add validation that this user can update this case.
        $data['name'] = $update_text;
        $data['description'] = $update_text;
        $data['contact_id'] = $contact_id;
        $data['case_id'] = $case_id;
        $response = $this->restClient->setEntry('AOP_Case_Updates',$data);
        return $this->getUpdate($response['id']);

    }

    public function getUpdate($update_id){
        $sugarupdate = $this->restClient->getEntry("AOP_Case_Updates",$update_id,$this->case_update_fields,
            array(
                array('name'=>'contact',
                    'value' =>
                        $this->contact_fields
                    ),
                array('name'=>'assigned_user_link',
                    'value' =>
                    $this->user_fields
               )));
        //TODO: Check exists
        $update =  new SugarUpdate($sugarupdate['entry_list'][0],$sugarupdate['relationship_list'][0]);
        return $update;
    }

    public function getNoteAttachment($note_id){
        $attachment = $this->restClient->get_note_attachment($note_id);
        return $attachment['note_attachment'];
    }

    public function setCaseStatus($caseId,$newStatus){
        $state = explode('_',$newStatus,2);
        $state = array_shift($state);
        $caseData = array(
            'id' => $caseId,
            'status' => $newStatus,
            'state' => $state,
        );
        return $this->restClient->setEntry('Cases',$caseData);
    }

    public function getCase($case_id,$contact_id){
        $sugarcase = $this->restClient->getEntry("Cases",$case_id,$this->case_fields,
        array(
            array('name'=>'aop_case_updates',
                'value' => $this->case_update_fields),
            array('name'=>'notes',
                'value' => $this->note_fields),
            array('name'=>'accounts',
                'value' => $this->account_fields),
            array('name'=>'contacts',
                'value' => array('id')),
        ));

         $case = new SugarCase($sugarcase['entry_list'][0],$sugarcase['relationship_list'][0]);

        $contact = $this->getContact($contact_id);
        $access = false;
        switch($contact->portal_user_type){
            case 'Distributor':
                foreach($case->accounts as $account){
                    if(($contact->account_id === $account->id)||($contact->account_id === $account->parent_id)){
                        $access = true;
                        break;
                    }
                }
                break;
            case 'Account':
                foreach($case->accounts as $account){
                    if($contact->account_id === $account->id){
                        $access = true;
                        break;
                    }
                }
                break;
            case 'Single':
            default:
                foreach($case->contacts as $caseContact){
                    if($contact->id === $caseContact->id){
                        $access = true;
                        break;
                    }
                }
                break;
        }
        if(!$access){
            return null;
        }

         //Grab all updates and related contacts in one go
        $sugarupdates = $this->restClient->getRelationships('Cases', $case_id,'aop_case_updates','',$this->case_update_fields,
            array(
                array('name'=>'contact',
                    'value' =>
                    $this->contact_fields
                ),
                array('name'=>'assigned_user_link',
                    'value' =>
                    $this->user_fields
                ),
                array('name'=>'notes',
                    'value' =>
                    $this->note_fields
                ))
        );

        $newupdates = array();
        foreach($sugarupdates['entry_list'] as $index => $sugarupdate){
            $update = new SugarUpdate($sugarupdate,$sugarupdates['relationship_list'][$index]);
            if($update->internal){
                continue;
            }
            $newupdates[] = $update;
        }
        usort($newupdates, function($a, $b){
            return strtotime($a->date_entered) - (strtotime($b->date_entered));
        });

        $case->aop_case_updates = $newupdates;
        return $case;
    }

    public function getContact($contactId){
        $sugarcontact = $this->restClient->getEntry("Contacts",$contactId,$this->contact_fields);
        $contact =  new SugarUpdate($sugarcontact['entry_list'][0],$sugarcontact['relationship_list'][0]); 
        return $contact;
    }
   

    public function getCases($contact_id){
        $contact = $this->getContact($contact_id);
       
        
        switch($contact->portal_user_type){
            case 'Distributor':
                $cases = $this->fromSugarCases($this->restClient->getRelationships('Accounts', $contact->account_id,'cases','',$this->case_fields));
                $accounts = $this->restClient->getEntryList('Accounts', "accounts.parent_id = '".$contact->account_id."'",'','',$this->account_fields);
                foreach($accounts['entry_list'] as $account){
                    $cases = $this->addSugarCases($cases,$this->restClient->getRelationships('Accounts', $account['id'],'cases','',$this->case_fields));
                }            
                break;
            case 'Account':
                $cases = $this->fromSugarCases($this->restClient->getRelationships('Accounts', $contact->account_id,'cases','',$this->case_fields));
                break;
            case 'Single':
            default:
                $cases = $this->fromSugarCases($this->restClient->getRelationships('Contacts', $contact_id,'cases','',$this->case_fields));
                break;
        }
        return $cases;
    }

    private function addSugarCases($cases, $sugarcases){
        
        foreach($sugarcases['entry_list'] as $sugarcase){
            $cases[] = new SugarCase($sugarcase);
        }
        return $cases;
    }
    private function fromSugarCases($sugarcases){
        $cases = array();
        foreach($sugarcases['entry_list'] as $sugarcase){
            $cases[] = new SugarCase($sugarcase);
        }
        return $cases;
    }

    public static function isValidPortalUser($user){
        return !empty($user->id) && $user->getParam("sugarid");
    }

    public static function isUserBlocked($user){
        return $user->getParam("aop_block");
    }

    private function getContactData($sugarId,$user){
        $data = array();
        if($sugarId){
            $data['id'] = $sugarId;
        }
        $name = explode(' ',$user['name'],2);

        $data['first_name'] = empty($name[0]) ? '' : $name[0];
        $data['last_name'] = empty($name[1]) ? '' : $name[1];
        $data['email'] = $user['email'];
        $data['email1'] = $user['email'];
        $data['joomla_account_id'] = $user['id'];
        return $data;
    }

    public function updateOrCreateContact($sugarId,$user){
        $contactData = $this->getContactData($sugarId, $user);
        $res = $this->restClient->setEntry('Contacts',$contactData);
        return $res;
    }

}