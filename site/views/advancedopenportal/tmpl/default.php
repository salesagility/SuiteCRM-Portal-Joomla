<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Document\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

$webAssetManager = Factory::getDocument()->getWebAssetManager();
$webAssetManager->registerAndUseStyle('portalcss','components/com_advancedopenportal/css/portal.css');

$user = Factory::getUser();
if($user->id){
    ?><h1><?php echo Text::_('COM_ADVANCEDOPENPORTAL_PORTAL_NAME');?></h1>
    <ul>
        <li><a href="<?php echo URI::base(); ?>index.php?option=com_advancedopenportal&view=newcase"><?php echo Text::_('COM_ADVANCEDOPENPORTAL_CREATE_CASE');?></a></li>
        <li><a href="<?php echo URI::base(); ?>index.php?option=com_advancedopenportal&view=listcases"><?php echo Text::_('COM_ADVANCEDOPENPORTAL_LIST_CASES');?></a></li>
    </ul>
    <?php
} else {
    echo '<h1>Home</h1>';

    echo Text::_('COM_ADVANCEDOPENPORTAL_LOGIN_REQUIRED');
}
