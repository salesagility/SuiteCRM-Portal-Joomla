<?php
// No direct access to this file
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Editor\Editor;
Use Joomla\CMS\Router\Route;

defined('_JEXEC') or die('Restricted access');

$webAssetManager = Factory::getDocument()->getWebAssetManager();
$webAssetManager->registerAndUseStyle('portalcss','components/com_advancedopenportal/css/portal.css');
$webAssetManager->useScript('form.validate');

$config = Factory::getConfig();
$editor = $config->get('editor');
$editor = Editor::getInstance($editor);


$descField = $editor->display( 'description', '', '100%', '450', '20', '20', false );

//HTMLHelper::_('behavior.formvalidation');
?>
<h2><?php echo Text::_('COM_ADVANCEDOPENPORTAL_CREATE_CASE');?></h2>
<form class="portal_form form-validate" action="<?php echo Route::_('index.php?option=com_advancedopenportal&task=newcase&format=raw'); ?>" method="post" id="newCaseForm" name="newCaseForm" enctype="multipart/form-data">
    <fieldset>
    <label for="subject"><?php echo Text::_('COM_ADVANCEDOPENPORTAL_CASE_SUBJECT');?>:</label><br><input type="text" name="subject" class='form-control required'><br>
        <?php
        if ($this->allow_type) {
            ?>
            <label for="type"><?php echo Text::_('COM_ADVANCEDOPENPORTAL_CASE_TYPE'); ?>:</label>
            <br>
            <select class="form-select valid" name="type">
                <?php
                foreach ($this->types['options'] as $type) {
                    ?>
                    <option value="<?php echo $type['name']; ?>" <?php echo isset($this->types['default_value']) && $this->types['default_value'] == $type['name'] ? 'selected="selected"' : ''; ?>><?php echo $type['value']; ?></option>
                    <?php
                }
                ?>
            </select>
            <br>
            <?php
        }
        if ($this->allow_priority) {
            ?>
            <label for="priority"><?php echo Text::_('COM_ADVANCEDOPENPORTAL_CASE_PRIORITY'); ?>:</label>
            <br>
            <select class="form-select valid" name="priority">
                <?php
                foreach ($this->priorities['options'] as $priority) {
                    ?>
                    <option value="<?php echo $priority['name']; ?>" <?php echo isset($this->priorities['default_value']) && $this->priorities['default_value'] == $priority['name'] ? 'selected="selected"' : ''; ?>><?php echo $priority['value']; ?></option>
                    <?php
                }
                ?>
            </select>
            <br>
            <?php
        }
        ?>
    <label for="description"><?php echo Text::_('COM_ADVANCEDOPENPORTAL_CASE_DESCRIPTION');?>:</label><br><?php echo $descField;?>
        <div class="case-save btn-toolbar float-end clearfix mt-3">
            <input class="btn btn-primary" id="new_case_submit" type="submit" value="<?php echo Text::_('COM_ADVANCEDOPENPORTAL_SAVE');?>">
        </div>
        <div class="doc-add clearfix">
            <input type="hidden" name="file_count" id="file_count" value="1"/>
            <input type="file" name="file1" id="file1">
            <a href="javascript:;" id="add_file"><?php echo Text::_('COM_ADVANCEDOPENPORTAL_CASE_ADD_ANOTHER_FILE');?></a>
        </div>
    </fieldset>
</form>

<script>
    jQuery(document).ready(function() {
        jQuery('#add_file').click(function(){
            var new_element = jQuery("#file1").clone();
            var file_count = jQuery("#file_count").val();
            file_count++;
            jQuery("#file_count").val(file_count);
            new_element.attr("id","file"+file_count);
            new_element.attr("name","file"+file_count);
            new_element.val("");

            var div = jQuery('<div>');
            div.insertBefore(jQuery("#add_file"));
            div.append(new_element);
            div.append(jQuery("<button type='button' class='remove_file' id='remove_file"+file_count+"'><?php echo Text::_('COM_ADVANCEDOPENPORTAL_CASE_REMOVE_FILE');?></button>"));
            return false;
        });
        jQuery('#newCaseForm').on('click','.remove_file',function(){
            jQuery(this).closest('div').remove()
        });
    } );
</script>
