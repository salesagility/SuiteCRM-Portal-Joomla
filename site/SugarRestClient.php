<?php

class SugarRestClient
{

    private static $singleton;

    protected $sid = null;

    private $rest_url = "";

    private $rest_user = "";

    private $rest_pass = "";

    private $token_type;

    private $token_expires;

    private $access_token;

    private $refresh_token;

    private function __construct()
    {
        include_once 'components/com_advancedopenportal/models/advancedopenportal.php';
        $settings = AdvancedOpenPortalModelAdvancedOpenPortal::getSettings();
        $this->rest_url = $settings->sugar_url;
        $this->base_url = $settings->sugar_url;
        $this->rest_user = $settings->sugar_user;
        $this->rest_pass = $settings->sugar_pass;
    }

    public static function getInstance()
    {
        if (!self::$singleton) {
            self::$singleton = new SugarRestClient();
        }
        return self::$singleton;
    }

    /**
     * Logout
     */
    public function logout()
    {
        $this->rest_request(
            'logout',
            array(
                'session' => $this->sid,
            )
        );

        $this->sid = null;
    }

    private function rest_request($api_route, $params = array(), $type = 'GET')
    {

        if (!$this->isLoggedIn()) {
            if (!$this->login()) {
                throw new Exception("Failed to connect to SuiteCRM. Please check your settings.");
            }
        }
        return json_decode($this->curlRequest($api_route, $params, $type), true);
    }

    private function isLoggedIn()
    {
        return isset($this->token_type);
    }

    public function login()
    {

        $params = array(
            'grant_type' => 'password',
            'client_id' => '1',
            'secret' => 'someSecret',
            'username' => $this->rest_user,
            'password' => 'suitecrm',
            'scope' => ''
        );
        $response_data = json_decode($this->curlRequest('/api/oauth/access_token', $params, 'POST'), true);

        $this->token_type = $response_data['token_type'];
        $this->token_expires = $response_data['expires_in'];
        $this->access_token = $response_data['access_token'];
        $this->refresh_token = $response_data['refresh_token'];

        return $this->isLoggedIn();
    }

    private function curlRequest($api_route, $params, $type = 'GET')
    {
        ob_start();
        $ch = curl_init();

        $this->lastData = $params;
        $this->lastUrl = $this->rest_url . $api_route;

        $postStr = json_encode($params);

        $header = array(
            'Content-type: application/vnd.api+json',
            'Accept: application/vnd.api+json',
        );

        if ($type != 'GET') {
            $header[] = 'Content-Length: ' . strlen($postStr);
        }

        if ($this->isLoggedIn()) {
            $header[] = 'Authorization: Bearer ' . $this->access_token;
        }
        curl_setopt($ch, CURLOPT_URL, $this->lastUrl);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postStr);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_COOKIE, 'XDEBUG_SESSION=1');

        $output = curl_exec($ch);

        curl_close($ch);

        ob_end_flush();

        if ($output === false) {
            echo 'Curl error: ' . curl_error($ch);
        }

        return $output;
    }

    public function getEntry(String $module, String $id)
    {

        $url = 'api/v8/modules/' . $module . '/' . $id;
        $result = $this->rest_request($url);

        return $this->evaluateResult($result);
    }

    private function evaluateResult($result, $key = '')
    {
        if (isset($result['errors']) && count($result['errors'])) {
            throw new Exception("Error while communicating with SuiteCRM: " . $result['errors'][0]['title']);
        }
        if ($key) {
            return $result[$key];
        }
        return $result;
    }

    public function getEntries(String $module, Array $ids)
    {

        $url = 'api/v8/modules/' . $module . '?filter[' . $module . ']=' . implode(',', $ids);
        $result = $this->rest_request($url);
        echo '<pre>';
        print_r($url);
        print_r($result);
        echo '</pre>';
        die();
        return $this->evaluateResult($result);
    }

    public function getApplicationLanguage()
    {

        $url = 'api/v8/modules/meta/languages';
        $result = $this->rest_request($url);

        $data = $this->evaluateResult($result);

        return $data['meta']['application']['language'];
    }

    public function setEntry($module, $data)
    {
        $id = isset($data['id']) ? $data['id'] : '';

        $postVars = array(
            'data' => array(
                'type' => $module,
                'attributes' => $data
            )
        );

        if ($id) {
            $postVars['data']['id'] = $id;
            $url = 'api/v8/modules/' . $module . '/' . $id;
            $result = $this->rest_request($url, $postVars, 'PATCH');
        } else {
            $url = 'api/v8/modules/' . $module;
            $result = $this->rest_request($url, $postVars, 'POST');
        }

        return $this->evaluateResult($result);
    }

    public function setRelationship($module1, $module1_id, $module2, $module2_id)
    {

        $data = array(
            'data' => array(
                'id' => $module2_id,
                'type' => $module2,
            )
        );

        $url = 'api/v8/modules/' . $module1 . '/' . $module1_id . '/relationships/' . $module2;
        $result = $this->rest_request($url, $data, 'POST');

        return $this->evaluateResult($result);
    }

    public function getRelationships($module_name, $module_id, $related_module)
    {

        $url = 'api/v8/modules/' . $module_name . '/' . $module_id . '/relationships/' . $related_module;
        $result = $this->rest_request($url);

        return $this->evaluateResult($result, 'data');
    }

    public function getModuleFields($module, $field)
    {
        return false;

        $url = 'api/v8/modules/' . $module . '/meta/attributes';
        $result = $this->rest_request($url);

        return $this->evaluateResult($result);
    }

    public function getAllModuleFields($module)
    {
        return false;

        $url = 'api/v8/modules/' . $module . '/meta/attributes';
        $result = $this->rest_request($url);

        return $this->evaluateResult($result);
    }

    public function get_note_attachment($note_id)
    {
        return false;

        $call_arguments = array(
            'session' => $this->sid,
            'id' => $note_id
        );

        $result = $this->rest_request(
            'get_note_attachment',
            $call_arguments
        );

        return $result;
    }

    public function set_note_attachment($note_id, $file_name, $file_location)
    {
        return false;

        $result = $this->rest_request(
            'set_note_attachment',
            array(
                'session' => $this->sid,
                'note' => array(
                    'id' => $note_id,
                    'filename' => $file_name,
                    'file' => base64_encode(file_get_contents($file_location)),
                ),
            )
        );

        return $result;
    }

    public function get_document_revision($id)
    {
        return false;

        $result = $this->rest_request(
            'get_document_revision',
            array(
                'session' => $this->sid,
                'id' => $id,
            )
        );

        return $result;
    }

    public function set_document_revision($document_id, $file_name, $file_location, $revision_number = 1)
    {
        return false;

        $result = $this->rest_request(
            'set_document_revision',
            array(
                'session' => $this->sid,
                'document_revision' => array(
                    'id' => $document_id,
                    'revision' => $revision_number,
                    'filename' => $file_name,
                    'file' => base64_encode(file_get_contents($file_location)),
                ),
            )
        );

        return $result;
    }
}
