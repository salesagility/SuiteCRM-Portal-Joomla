<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
?>

<fieldset class="options-form">
    <legend>SuiteCRM Connection Settings</legend>
    <div class="form-grid">
        <div class="control-group">
            <div class="control-label">
                <label>SuiteCRM URL</label>
            </div>
            <div class="controls">
                <input class="form-control" type="text" size="40" value="<?php echo $this->items->sugar_url ?? '' ?>" name="sugar_url">
            </div>
        </div>
        <div class="control-group">
            <div class="control-label">
                <label>SuiteCRM Username</label>
            </div>
            <div class="controls">
                <input class="form-control" type="text" size="40" value="<?php echo $this->items->sugar_user  ?? '' ?>" name="sugar_user">
            </div>
        </div>
        <div class="control-group">
            <div class="control-label">
                <label>SuiteCRM Password</label>
            </div>
            <div class="controls">
                <input class="form-control" type="password" size="40" value="" <?php echo $this->items->sugar_pass ? 'placeholder="Password Unchanged"' : '' ?> name="sugar_pass">
            </div>
        </div>
    </div>
</fieldset>

<fieldset class="options-form">
    <legend>Portal Config</legend>
    <div class="form-grid">
        <div class="control-group">
            <div class="control-label">
                <label>Allow reopening cases</label>
            </div>
            <div class="controls">
                <input type="checkbox" <?php echo $this->items->allow_case_reopen  ?? false  ? 'checked="checked"' : '';?> name="allow_case_reopen" id="allow_case_reopen">
            </div>
        </div>
        <div class="control-group">
            <div class="control-label">
                <label>Allow closing cases</label>
            </div>
            <div class="controls">
                <input type="checkbox" <?php echo $this->items->allow_case_closing  ?? false ? 'checked="checked"' : '';?> name="allow_case_closing" id="allow_case_closing">
            </div>
        </div>
        <div class="control-group">
            <div class="control-label">
                <label>Allow choosing case priority</label>
            </div>
            <div class="controls">
                <input type="checkbox" <?php echo $this->items->allow_priority  ?? false ? 'checked="checked"' : '';?> name="allow_priority" id="allow_priority">
            </div>
        </div>
        <div class="control-group">
            <div class="control-label">
                <label>Allow choosing case type</label>
            </div>
            <div class="controls">
                <input type="checkbox" <?php echo $this->items->allow_type ?? false ? 'checked="checked"' : '';?> name="allow_type" id="allow_type">
            </div>
        </div>
    </div>
</fieldset>