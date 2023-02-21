<?php
// No direct access to this file
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die('Restricted access');

$webAssetManager = Factory::getDocument()->getWebAssetManager();
$webAssetManager->registerAndUseStyle('portalcss','components/com_advancedopenportal/css/portal.css');
$webAssetManager->registerAndUseStyle('dataTablesCss','components/com_advancedopenportal/css/jquery.dataTables.min.css');
$webAssetManager->registerAndUseScript('dataTables','components/com_advancedopenportal/js/jquery.dataTables.min.js');

if (!$this->validPortalUser || $this->userBlocked) {
    return;
}

?>

<div class="case-filter btn-toolbar clearfix mt-3" id="select_controls">
    <div class="filter-search-bar btn-group">
        <input class="form-control" type="text" id="case_text_search" placeholder="<?php echo Text::_('COM_ADVANCEDOPENPORTAL_SEARCH');?>">
    </div>
    &nbsp;
    <div class="filter-search-bar btn-group">
    <select id="status_select" class="form-select">
        <option value=""><?php echo Text::_('COM_ADVANCEDOPENPORTAL_CASE_STATUS_ALL');?></option>
        <?php
        foreach($this->states['options'] as $state){
            ?>
            <option value="<?php echo $state['name'];?>"><?php echo $state['value'];?></option>
            <?php
        }
        ?>
    </select>
    </div>
    <?php
    if($this->contact->portal_user_type === 'Account'){
        ?>
        &nbsp;
    <div class="own_filter-bar btn-group">
        <label><?php echo Text::_('COM_ADVANCEDOPENPORTAL_OWN_CASES');?>: <input type="checkbox" name="own_filter" id="own_filter"></label>
    </div>
        <?php
    }
    ?>
    <div class="new-case float-end">
        <form action="<?php echo URI::base(); ?>index.php" method="get">
            <input type="hidden" name="option" value="com_advancedopenportal">
            <input type="hidden" name="view" value="newcase">
            <input class="btn btn-secondary" type="submit" value="<?php echo Text::_('COM_ADVANCEDOPENPORTAL_CREATE_CASE');?>">
        </form>
    </div>
</div>

<table id='case_table' class="display compact">
    <thead>
    <tr>
        <th><?php echo Text::_('COM_ADVANCEDOPENPORTAL_CASE_NUMBER');?></th>
        <th><?php echo Text::_('COM_ADVANCEDOPENPORTAL_CASE_SUBJECT');?></th>
        <th><?php echo Text::_('COM_ADVANCEDOPENPORTAL_CASE_STATUS');?></th>
        <th><?php echo Text::_('COM_ADVANCEDOPENPORTAL_CASE_STATE');?></th>
        <th><?php echo Text::_('COM_ADVANCEDOPENPORTAL_CASE_CREATED_BY');?></th>
        <th><?php echo Text::_('COM_ADVANCEDOPENPORTAL_CASE_CREATED_BY');?></th>
        <th><?php echo Text::_('COM_ADVANCEDOPENPORTAL_CASE_CREATED');?></th>
        <th><?php echo Text::_('COM_ADVANCEDOPENPORTAL_CASE_LAST_UPDATE');?></th>
    </tr>
    </thead>
    <tbody>
<?php
foreach($this->cases as $case){
?>
    <tr>
        <td><?php echo $case->case_number;?></td>
        <td><a href='?option=com_advancedopenportal&view=showcase&id=<?php echo $case->id;?>'><?php echo $case->name;?></a></td>
        <td><?php echo $case->status_display;?></td>
        <td><?php echo $case->state;?></td>
        <td><?php echo $case->contact_created_by_name;?></td>
        <td><?php echo $case->contact_created_by_id;?></td>
        <td><?php echo $case->date_entered_display;?></td>
        <td><?php echo $case->date_modified_display;?></td>
    </tr>
<?php
}
?>
    </tbody>
</table>


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
                {"sWidth": "40%", "aTargets": [1]},
                {"sWidth": "10%", "aTargets": [2]},
                { "bVisible": false, "aTargets": [3]},
                {"sWidth": "15%", "aTargets": [4]},
                { "bVisible": false, "aTargets": [5]},
                {"sWidth": "15%", "aTargets": [6]},
                {"sWidth": "15%", "aTargets": [7]}
            ],
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
