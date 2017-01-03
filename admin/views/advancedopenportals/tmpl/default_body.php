<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
?>

<fieldset>
    <legend><?php echo JText::_('COM_ADVANCEDOPENPORTAL_PORTAL_SETTINGS');?></legend>
    <table class="adminlist">
        <tbody>
            <tr class="row0">
		        <td><?php echo JText::_('COM_ADVANCEDOPENPORTAL_CRM_URL');?></td><td><input type="text" size="40" value="<?php echo $this->items->sugar_url ?>" name="sugar_url"></td>
            </tr>
            <tr class="row1">
                <td><?php echo JText::_('COM_ADVANCEDOPENPORTAL_CRM_USERNAME');?></td><td><input type="text" value="<?php echo $this->items->sugar_user ?>" name="sugar_user"></td>
            </tr>
            <tr class="row0">
                <td><?php echo JText::_('COM_ADVANCEDOPENPORTAL_CRM_PASSWORD');?></td><td><input type="password" value="<?php echo $this->items->sugar_pass ?>" name="sugar_pass"></td>
	        </tr>
            <tr class="row1">
                <td><label for="allow_case_reopen"><?php echo JText::_('COM_ADVANCEDOPENPORTAL_CRM_CASES_ALLOW_REOPENING');?></label></td><td><input type="checkbox" <?php echo $this->items->allow_case_reopen ? 'checked="checked"' : '';?> name="allow_case_reopen" id="allow_case_reopen"></td>
            </tr>
            <tr class="row0">
                <td><label for="allow_case_closing"><?php echo JText::_('COM_ADVANCEDOPENPORTAL_CRM_CASES_ALLOW_CLOSING');?></label></td><td><input type="checkbox" <?php echo $this->items->allow_case_closing ? 'checked="checked"' : '';?> name="allow_case_closing" id="allow_case_closing"></td>
            </tr>
            <tr class="row1">
                <td><label for="allow_priority"><?php echo JText::_('COM_ADVANCEDOPENPORTAL_CRM_CASES_ALLOW_CHOOSING_PRIORITY');?></label></td><td><input type="checkbox" <?php echo $this->items->allow_priority ? 'checked="checked"' : '';?> name="allow_priority" id="allow_priority"></td>
            </tr>
            <tr class="row0">
                <td><label for="allow_type"><?php echo JText::_('COM_ADVANCEDOPENPORTAL_CRM_CASES_ALLOW_CHOOSING_TYPE');?></label></td><td><input type="checkbox" <?php echo $this->items->allow_type ? 'checked="checked"' : '';?> name="allow_type" id="allow_type"></td>
            </tr>
        </tbody>
    </table>
</fieldset>