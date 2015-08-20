<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
 
// load tooltip behavior
JHtml::_('behavior.tooltip');

?>
<form action="<?php echo JRoute::_('index.php?option=com_advancedopenportal'); ?>" method="post" name="adminForm">
            <?php echo $this->loadTemplate('head');?>
            <?php echo $this->loadTemplate('body');?>
            <?php echo $this->loadTemplate('foot');?>
</form>
