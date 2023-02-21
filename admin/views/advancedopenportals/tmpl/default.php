<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')->useScript('form.validate');

?>

<form action="<?php echo Route::_('index.php?option=com_advancedopenportal'); ?>" id="advancedopenportal-form" method="post" name="adminForm" class="main-card form-validate">
    <div class="row main-card-columns">
            <?php echo HTMLHelper::_('uitab.startTabSet', 'configTabs', ['active' => 'page-site', 'recall' => true, 'breakpoint' => 768]); ?>
            <?php echo HTMLHelper::_('uitab.addTab', 'configTabs', 'page-site', 'Settings'); ?>
            <?php echo $this->loadTemplate('body'); ?>

            <input type="hidden" name="task" value="">
            <?php echo HTMLHelper::_('form.token'); ?>
    </div>
</form>
