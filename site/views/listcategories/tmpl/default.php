<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
jimport('joomla.html.pagination');

$document = &JFactory::getDocument();
$user =& JFactory::getUser();

if($this->userBlocked){
    return;
}
$pageNav = new JPagination($this->count, $this->start, $this->limit);
?>

<div class="categories-list">
    <div>
        <form name="category_search"  method="post" style="margin-bottom: 0;" action="<?php echo JRoute::_('index.php?option=com_advancedopenportal&task=article_search');?>">
            <fieldset>
                <input id="cat_search" class="inputbox search-query" name="cat_search" type="text" value="Search..."  onfocus="if (this.value=='Search...') this.value='';" onblur="if (this.value=='') this.value='Search...';">
                <button>
                    Search
                </button>
            </fieldset>
        </form>
    </div>
    <form id="adminForm" class="form-inline" name="adminForm" method="post" action="<?php echo JRoute::_('index.php?option=com_advancedopenportal&view=listcategories');?>" >
        <fieldset class="filters btn-toolbar clearfix">
            <div class="btn-group pull-right">
                <?php echo $pageNav->getLimitBox(); ?>
            </div>
        </fieldset>
    <?php
    $i = 0;
    $len = count($this->categories);
    foreach ($this->categories as $category) {
        if ($i == 0) {
            echo '<div class="first"><h3 class="page-header item-title">';
            echo '<a href="index.php?option=com_advancedopenportal&view=listarticles&id='.$category['id']['value'].'">'.$category['name']['value'].'</a> ';
            echo '<span class="badge badge-info tip hasTooltip" title="" data-original-title="Article Count:">'.$category['count'].'</span></h3></div>';
        } else if ($i == $len - 1) {
            echo '<div class="last"><h3 class="page-header item-title">';
            echo '<a href="index.php?option=com_advancedopenportal&view=listarticles&id='.$category['id']['value'].'">'.$category['name']['value'].'</a> ';
            echo '<span class="badge badge-info tip hasTooltip" title="" data-original-title="Article Count:">'.$category['count'].'</span></h3></div>';
        }
        else {
            echo '<div><h3 class="page-header item-title">';
            echo '<a href="index.php?option=com_advancedopenportal&view=listarticles&id='.$category['id']['value'].'">'.$category['name']['value'].'</a> ';
            echo '<span class="badge badge-info tip hasTooltip" title="" data-original-title="Article Count:">'.$category['count'].'</span></h3></div>';
        }

        $i++;
    }

    ?>
    <?php
    echo $pageNav->getListFooter(); //Displays a nice footer
    ?>
        </form>
</div>

