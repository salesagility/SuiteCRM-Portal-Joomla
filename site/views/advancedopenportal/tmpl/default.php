<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$document = &JFactory::getDocument();
$document->addStyleSheet('components'.DIRECTORY_SEPARATOR.'com_advancedopenportal'.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.'portal.css');
$user =& JFactory::getUser();
if($user->id){
    ?><h1><?php echo JText::_('COM_ADVANCEDOPENPORTAL_PORTAL_NAME');?></h1>
    <ul>
        <li><a href="<?php echo JURI::base(); ?>index.php?option=com_advancedopenportal&view=newcase"><?php echo JText::_('COM_ADVANCEDOPENPORTAL_CREATE_CASE');?></a></li>
        <li><a href="<?php echo JURI::base(); ?>index.php?option=com_advancedopenportal&view=listcases"><?php echo JText::_('COM_ADVANCEDOPENPORTAL_LIST_CASES');?></a></li>
    </ul>
    <?php
} else {
    echo '<h1>Home</h1>';

    echo JText::_('COM_ADVANCEDOPENPORTAL_LOGIN_REQUIRED');
}
