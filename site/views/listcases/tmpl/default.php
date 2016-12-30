<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$document = &JFactory::getDocument();
$document->addStyleSheet('components'.DIRECTORY_SEPARATOR.'com_advancedopenportal'.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.'portal.css');
$document->addScript('components'.DIRECTORY_SEPARATOR.'com_advancedopenportal'.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.'jquery.2.0.0.min.js');
$document->addScript('components'.DIRECTORY_SEPARATOR.'com_advancedopenportal'.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.'jquery.dataTables.1.9.4.min.js');

$user =& JFactory::getUser();

if(!$this->validPortalUser || $this->userBlocked){
    return;
}

?>

<div id="select_controls" style="display: inline;">
<label for="status_select"><?php echo JText::_('COM_ADVANCEDOPENPORTAL_CASE_STATUS');?>:</label></label><select id="status_select">
    <option value=""><?php echo JText::_('COM_ADVANCEDOPENPORTAL_CASE_STATUS_ALL');?></option>
        <?php
        foreach($this->states['options'] as $state){
            ?>
            <option value="<?php echo $state['name'];?>"><?php echo $state['value'];?></option>
            <?php
        }
        ?>
</select>
    <?php
    if($this->contact->portal_user_type === 'Account'){
    ?>
    <label><?php echo JText::_('COM_ADVANCEDOPENPORTAL_OWN_CASES');?>: <input type="checkbox" name="own_filter" id="own_filter"></label>
    <?php
    }
    ?>
    <label><?php echo JText::_('COM_ADVANCEDOPENPORTAL_SEARCH');?>: <input type="text" id="case_text_search" placeholder=""></label>
</div>
<table id='case_table'>
    <thead>
    <tr>
        <th><?php echo JText::_('COM_ADVANCEDOPENPORTAL_CASE_NUMBER');?></th>
        <th><?php echo JText::_('COM_ADVANCEDOPENPORTAL_CASE_SUBJECT');?></th>
        <th><?php echo JText::_('COM_ADVANCEDOPENPORTAL_CASE_PRODUCT');?></th>
        <th><?php echo JText::_('COM_ADVANCEDOPENPORTAL_CASE_STATUS');?></th>
        <th><?php echo JText::_('COM_ADVANCEDOPENPORTAL_CASE_STATE');?></th>
        <th><?php echo JText::_('COM_ADVANCEDOPENPORTAL_CASE_CREATED_BY');?></th>
        <th><?php echo JText::_('COM_ADVANCEDOPENPORTAL_CASE_CREATED_BY');?></th>
        <th><?php echo JText::_('COM_ADVANCEDOPENPORTAL_CASE_CREATED');?></th>
        <th><?php echo JText::_('COM_ADVANCEDOPENPORTAL_CASE_LAST_UPDATE');?></th>
    </tr>
    </thead>
    <tbody>
<?php
foreach($this->cases as $case){
?>
    <tr>
        <td><?php echo $case->case_number;?></td>
        <td><a href='?option=com_advancedopenportal&view=showcase&id=<?php echo $case->id;?>'><?php echo $case->name;?></a></td>
        <td><?php echo $case->product_c;?></td>
        <td><?php echo $case->status_display;?></td>
        <td><?php echo $case->state;?></td>
        <td><?php echo $case->contact_created_by_name;?></td>
        <td><?php echo $case->contact_created_by_id;?></td>
        <td><?php echo $case->date_entered_display;?></td>
        <td><?php echo $case->account_id;?></td>
    </tr>
    
<?php
}
?>
    </tbody>
</table>

<form action="<?php echo JURI::base(); ?>index.php" method="get">
    <input type="hidden" name="option" value="com_advancedopenportal">
    <input type="hidden" name="view" value="newcase">
    <input class="button" type="submit" value="<?php echo JText::_('COM_ADVANCEDOPENPORTAL_CREATE_CASE');?>">
</form>



<script>
    $.fn.dataTableExt.afnFiltering.push(
        function( oSettings, aData, iDataIndex ) {
            var chosen = $("#status_select").val();
            if(!chosen){
                return true;
            }
            var rowVal = aData[3];
            return rowVal == chosen;
        }
    );
    $.fn.dataTableExt.afnFiltering.push(
        function( oSettings, aData, iDataIndex ) {
            var own = $("#own_filter").is(":checked");
            if(!own){
                return true;
            }
            var rowVal = aData[5];
            return rowVal === '<?php echo $this->contact->id?>';
        }
    );
    $(document).ready(function() {
        var table = $('#case_table').dataTable({
            "bFilter": true,
            "bStateSave": true,
            "sDom": '<"table_controls"r>tip',
            "aaSorting": [[ 0, "desc" ]],
            "aoColumnDefs": [
                {"sWidth": "5%", "aTargets": [0]},
                {"sWidth": "45%", "aTargets": [1]},
                {"sWidth": "10%", "aTargets": [2]},
                {"sWidth": "10%", "aTargets": [3]},
                { "bVisible": false, "aTargets": [4]},
                {"bVisible": false, "aTargets": [5]},
                { "bVisible": false, "aTargets": [6]},
                {"sWidth": "15%", "aTargets": [6]},
                {"sWidth": "15%", "aTargets": [7]}
            ]
        });
        $("#select_controls").prependTo(".table_controls");
        $("#status_select").change(function(){
            table.fnDraw();
        });
        $("#own_filter").change(function(){
            table.fnDraw();
        });
        $('#case_text_search').keyup(function(){
            table.fnFilter( $(this).val() );
        })
    } );

</script>
