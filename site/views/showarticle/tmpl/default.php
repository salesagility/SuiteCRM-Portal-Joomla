<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$document = &JFactory::getDocument();
$user =& JFactory::getUser();

if($this->userBlocked){
    return;
}
?>

<div class="item-page">
    <div class="page-header">
        <h2 itemprop="name">
            <a href="index.php?option=com_advancedopenportal&view=showarticle&id=<?php echo $this->article['id']['value'];?>"><?php echo $this->article['name']['value'];?></a>
        </h2>
    </div>
    <div class="article-info muted">
        <dl class="article-info">
            <dt class="article-info-term"><?php echo JText::_('COM_ADVANCEDOPENKNOWLEDGEBASE_ARTICLE_DETAILS');?></dt>
                <dd class="createdby" itemprop="author">
                    <?php echo JText::_('COM_ADVANCEDOPENKNOWLEDGEBASE_ARTICLE_WRITTEN_BY'); ?>
                    <span itemprop="name"><?php echo $this->article['author']['value'];?></span>
                </dd>
                <dd class="category-name">
                    <?php echo JText::_('COM_ADVANCEDOPENKNOWLEDGEBASE_ARTICLE_CATEGORY');
                    $i = 0;
                    $len = count($this->cat);
                    foreach($this->cat as $cat){
                        if ($i == $len - 1) {
                            echo ' <a href="index.php?option=com_advancedopenportal&view=listarticles&id='.$cat['id']['value'].'" >'.$cat['name']['value'].'</a>';
                        }
                        else {
                            echo ' <a href="index.php?option=com_advancedopenportal&view=listarticles&id='.$cat['id']['value'].'" >' . $cat['name']['value'] . '</a>, ';
                        }
                        $i++;
                    }
                    ?>
                </dd>
                <dd class="published">
                    <span class="icon-calendar"></span>
                    <time itemprop="datePublished" datetime="<?php echo $this->article['date_modified']['value'];?>"><?php $t = strtotime($this->article['date_modified']['value']); echo date('F j, Y',$t)?></time>
                </dd>
        </dl>
    </div>
    <div itemprop="articleBody">
        <?php echo html_entity_decode($this->article['description']['value']);?>
    </div>
   <!-- <ul class="pager pagenav">
        <li class="previous">
            <a rel="prev" href="#">< Prev</a>
        </li>
        <li class="next">
            <a rel="next" href="#">Next ></a>
        </li>
    </ul>-->

</div>