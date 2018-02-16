<?php

class SugarRestClient
{

    private static $singleton;

    protected $sid = null;

    private $rest_url = "http://php71/SuiteCRM-github-develop/api/";

    private $rest_user = "admin";

    private $rest_pass = "suitecrm";

    private $rest_client = "1";

    private $rest_secret = "client_secret";

    private $token_type;

    private $token_expires;

    private $access_token;

    private $refresh_token;

    private function __construct()
    {
        include_once 'components/com_advancedopenportal/models/advancedopenportal.php';
        $settings = AdvancedOpenPortalModelAdvancedOpenPortal::getSettings();

        return;

        $this->rest_url = rtrim($settings->sugar_url, '/');
        $this->rest_url .= '/';
        $this->rest_url = str_replace('/api/', '', $this->rest_url);
        $this->rest_url .= 'api/';
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

    /**
     * @param $api_route
     * @param array $params
     * @param string $type
     * @return mixed
     */
    private function rest_request($api_route, $params = array(), $type = 'GET')
    {

        if (!$this->isLoggedIn()) {
            $this->login();
        }
        $result = $this->curlRequest($api_route, $params, $type);

        return json_decode($result, true);
    }

    private function isLoggedIn()
    {
        return isset($this->token_type);
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function login()
    {

        $params = array(
            'grant_type' => 'password',
            'client_id' => $this->rest_client,
            'client_secret' => $this->rest_secret,
            'username' => $this->rest_user,
            'password' => $this->rest_pass,
            'scope' => ''
        );

        $response_data = json_decode($this->curlRequest('oauth/access_token', $params, 'POST'), true);

        if (!empty($response_data['error'])) {
            throw new Exception(
                "Failed to connect to SuiteCRM. Please check your settings. (Error: "
                . $response_data['error']
                . ', '
                . $response_data['message']
                . ')'
            );
        }

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
        curl_setopt($ch, CURLOPT_COOKIE, 'XDEBUG_SESSION_START=13537');

        $output = curl_exec($ch);

        curl_close($ch);

        ob_end_flush();

        if ($output === false) {
            echo 'Curl error: ' . curl_error($ch);
        }

        return $output;
    }

    public function getEntry(String $module, String $id, $fields = [])
    {
        $fieldStr = '';
        if (count($fields)) {
            $fieldStr = '&fields[' . $module . ']' . implode(',', $fields);
        }

        $url = 'v8/modules/' . $module . '/' . $id . $fieldStr;
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

        $url = 'v8/modules/' . $module . '?filter[' . $module . ']=' . implode(',', $ids);
        $result = $this->rest_request($url);
        return $this->evaluateResult($result);
    }

    public function getApplicationLanguage()
    {

        $url = 'v8/modules/meta/languages';
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
            $url = 'v8/modules/' . $module . '/' . $id;
            $result = $this->rest_request($url, $postVars, 'PATCH');
        } else {
            $url = 'v8/modules/' . $module;
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

        $url = 'v8/modules/' . $module1 . '/' . $module1_id . '/relationships/' . $module2;
        $result = $this->rest_request($url, $data, 'POST');

        return $this->evaluateResult($result);
    }

    public function getRelationships($module_name, $module_id, $related_module)
    {

        $url = 'v8/modules/' . $module_name . '/' . $module_id . '/relationships/' . $related_module;
        $result = $this->rest_request($url);

        return $this->evaluateResult($result, 'data');
    }

    public function getModuleFields($module, $field)
    {
        return false;

        $url = 'v8/modules/' . $module . '/meta/attributes';
        $result = $this->rest_request($url);

        return $this->evaluateResult($result);
    }

    public function getAllModuleFields($module)
    {
        return false;

        $url = 'v8/modules/' . $module . '/meta/attributes';
        $result = $this->rest_request($url);

        return $this->evaluateResult($result);
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
