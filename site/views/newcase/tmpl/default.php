<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');


$user =& JFactory::getUser();
$document = &JFactory::getDocument();
$document->addStyleSheet('components'.DIRECTORY_SEPARATOR.'com_advancedopenportal'.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.'portal.css');
$document->addScript('components'.DIRECTORY_SEPARATOR.'com_advancedopenportal'.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.'jquery.2.0.0.min.js');

$editor =& JFactory::getEditor();
$params = array();
$descField = $editor->display( 'description', '', '', '', '20', '20', false, null, null, null, $params );

JHTML::_('behavior.formvalidation');
?>
<h2><?php echo JText::_('COM_ADVANCEDOPENPORTAL_CREATE_CASE');?></h2>
<form class="portal_form form-validate" action="<?php echo JRoute::_('index.php?option=com_advancedopenportal&task=newcase&format=raw'); ?>" method="post" id="newCaseForm" name="newCaseForm" enctype="multipart/form-data">
    <fieldset>
    <label for="subject"><?php echo JText::_('COM_ADVANCEDOPENPORTAL_CASE_SUBJECT');?>:</label><br><input type="text" name="subject" class='required'><br>
        <?php
        if($this->allow_type) {
            ?>
            <label for="type"><?php echo JText::_('COM_ADVANCEDOPENPORTAL_CASE_TYPE'); ?>:</label><br><select
                name="type">
                <?php
                foreach ($this->types as $name => $type) {
                    ?>
                    <option value="<?php echo $name ?>"><?php echo $type ?></option>
                <?php
                }
                ?>
            </select><br>
        <?php
        }
        if($this->allow_priority) {
        ?>
            <label for="priority"><?php echo JText::_('COM_ADVANCEDOPENPORTAL_CASE_PRIORITY');?>:</label><br><select name="priority">
                <?php
                foreach($this->priorities as $name => $priority){
                    ?>
                    <option value="<?php echo $name ?>"><?php echo $priority ?></option>
                    <?php
                }
                ?>
            </select><br>
    <?php
        }
    ?>
    <label for="description"><?php echo JText::_('COM_ADVANCEDOPENPORTAL_CASE_DESCRIPTION');?>:</label><br><?php echo $descField;?><br>
    <input type="file" name="file1" id="file1">
    <a href="javascript:;" id="add_file"><?php echo JText::_('COM_ADVANCEDOPENPORTAL_CASE_ADD_ANOTHER_FILE');?></a>
    <input type="hidden" name="file_count" id="file_count" value="1"/>
    </fieldset>
    <input class="button" id="new_case_submit" type="submit" value="<?php echo JText::_('COM_ADVANCEDOPENPORTAL_SAVE');?>">

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
            div.append(jQuery("<button type='button' class='remove_file' id='remove_file"+file_count+"'><?php echo JText::_('COM_ADVANCEDOPENPORTAL_CASE_REMOVE_FILE');?></button>"));
            return false;
        });
        jQuery('#newCaseForm').on('click','.remove_file',function(){
            jQuery(this).closest('div').remove()
        });
    } );
</script>
