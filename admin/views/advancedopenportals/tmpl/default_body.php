<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
?>

<fieldset>
    <legend>Portal Settings</legend>
    <table class="adminlist">
        <tbody>
            <tr class="row0">
		        <td>Sugar URL</td><td><input type="text" size="40" value="<?php echo $this->items->sugar_url ?>" name="sugar_url"></td>
            </tr>
            <tr class="row1">
                <td>Sugar Username</td><td><input type="text" value="<?php echo $this->items->sugar_user ?>" name="sugar_user"></td>
            </tr>
            <tr class="row0">
                <td>Sugar Password</td><td><input type="password" value="<?php echo $this->items->sugar_pass ?>" name="sugar_pass"></td>
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