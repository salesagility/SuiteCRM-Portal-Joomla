<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
?>

<fieldset>
    <legend>Portal Settings</legend>
    <table class="adminlist">
        <tbody>
            <tr class="row0">
		        <td>SuiteCRM URL</td><td><input type="text" size="40" value="<?php echo $this->items->sugar_url ?>" name="sugar_url"></td>
            </tr>
            <tr class="row1">
                <td>OAuth2 Client ID (should be 'Client Credentials' type)</td><td><input type="text" value="<?php echo $this->items->client_id ?>" name="client_id"></td>
            </tr>
            <tr class="row0">
                <td>OAuth2 Client Secret</td><td><input type="text" value="<?php echo $this->items->client_secret ?>" name="client_secret"></td>
	        </tr>
            <tr class="row1">
                <td><label for="allow_case_reopen">Allow reopening cases</label></td><td><input type="checkbox" <?php echo $this->items->allow_case_reopen ? 'checked="checked"' : '';?> name="allow_case_reopen" id="allow_case_reopen"></td>
            </tr>
            <tr class="row0">
                <td><label for="allow_case_closing">Allow closing cases</label></td><td><input type="checkbox" <?php echo $this->items->allow_case_closing ? 'checked="checked"' : '';?> name="allow_case_closing" id="allow_case_closing"></td>
            </tr>
            <tr class="row1">
                <td><label for="allow_priority">Allow choosing case priority</label></td><td><input type="checkbox" <?php echo $this->items->allow_priority ? 'checked="checked"' : '';?> name="allow_priority" id="allow_priority"></td>
            </tr>
            <tr class="row0">
                <td><label for="allow_type">Allow choosing case type</label></td><td><input type="checkbox" <?php echo $this->items->allow_type ? 'checked="checked"' : '';?> name="allow_type" id="allow_type"></td>
            </tr>
        </tbody>
    </table>
</fieldset>