<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport('joomla.html.pagination');

$document = &JFactory::getDocument();
$user =& JFactory::getUser();
$option = JRequest::getCmd('option');

if($this->userBlocked){
    return;
}
    $pageNav = new JPagination($this->count, $this->start, $this->limit);
    ?>
    <div>
        <form name="category_search"  method="post" style="margin-bottom: 0;" action="<?php echo JRoute::_('index.php?option=com_advancedopenportal&task=article_search');?>">
            <fieldset>
                <input id="cat_search" class="inputbox search-query" name="cat_search" type="text" value="<?php echo $this->search_query ?>" onclick='javascript: this.value = ""' ">
                <button>
                    Search
                </button>
            </fieldset>
        </form>
    </div>
    <form id="adminForm" class="form-inline" name="adminForm" method="post" action="<?php echo JRoute::_('index.php?option=com_advancedopenportal&task=article_search');?>">
        <fieldset class="filters btn-toolbar clearfix">
            <div class="btn-group pull-right">
                <?php echo $pageNav->getLimitBox(); ?>
            </div>
        </fieldset>
        <table class="category table table-striped table-bordered table-hover">
            <thead>
            <tr>
                <th id="categorylist_header_title"><?php echo JText::_('COM_ADVANCEDOPENKNOWLEDGEBASE_CATEGORY_TITLE');?></th>
                <th id="categorylist_header_author"><?php echo JText::_('COM_ADVANCEDOPENKNOWLEDGEBASE_CATEGORY_AUTHOR');?></th>
                <th id="categorylist_header_hits"><?php echo JText::_('COM_ADVANCEDOPENKNOWLEDGEBASE_CATEGORY_PUBLISHED');?></th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($this->articles as $article) {
                if ($article['status']['value'] == 'published_public') {
                    ?>
                    <tr>
                        <td class="list-title"><a
                                href="index.php?option=com_advancedopenportal&view=showarticle&id=<?php echo $article['id']['value'];?>"><?php echo $article['name']['value'];?></a>
                        </td>
                        <td class="list-author"><?php echo JText::_('COM_ADVANCEDOPENKNOWLEDGEBASE_ARTICLE_WRITTEN_BY') . ' ' . $article['author']['value'];?></td>
                        <td class=""><?php $t = strtotime($article['date_modified']['value']);
                            echo date('F j, Y', $t)?></td>
                    </tr>

                <?php
                }
            }
            ?>
            </tbody>
        </table>
        <?php
        echo $pageNav->getListFooter(); //Displays a nice footer
        ?>
    </form>
<?php
