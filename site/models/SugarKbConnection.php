<?php
/**
 *
 * @package Advanced OpenPortal
 * @copyright SalesAgility Ltd http://www.salesagility.com
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU AFFERO GENERAL PUBLIC LICENSE
 * along with this program; if not, see http://www.gnu.org/licenses
 * or write to the Free Software Foundation,Inc., 51 Franklin Street,
 * Fifth Floor, Boston, MA 02110-1301  USA
 *
 * @author Salesagility Ltd <support@salesagility.com>
 */
include_once 'components/com_advancedopenportal/sugarRestClient.php';
/*include_once 'components/com_advancedopenportal/models/SugarCase.php';
include_once 'components/com_advancedopenportal/models/SugarUpdate.php';*/


class SugarKbConnection {

    private static $singleton;

    public function __construct() {

        $this->restClient = new sugarRestClient();
        $this->cache = & JFactory::getCache();
        $this->cache->setCaching( 1 );
        if(!$this->restClient->login()){
            throw new Exception("Failed to connect to sugar. Please check your settings.");
        }
    }

    public static function getInstance(){
        if(!self::$singleton){
            self::$singleton = new SugarKbConnection();
        }
        return self::$singleton;
    }

    //need to add method for getting categories out of the array.
    public function getCategories($offset=0, $limit=10){

        $category_list = $this->restClient->getEntryList('AOK_Knowledge_Base_Categories','','name',$offset,'','',$limit);

        $categories= array();
        if($category_list !=false){
            foreach($category_list['entry_list'] as $cat) {
                $cat_count = $this->categoryCount($cat['id']);
                $cat['name_value_list']['count'] = $cat_count;
                $categories[] = $cat['name_value_list'];
            }
        }
        return $categories;
    }

    //returns the number of categories
    public function countCategories(){

        $category_list = $this->restClient->getEntryList('AOK_Knowledge_Base_Categories','','','','');

        $categories= array();
        if($category_list !=false){
            foreach($category_list['entry_list'] as $cat) {
                $cat_count = $this->categoryCount($cat['id']);
                $cat['name_value_list']['count'] = $cat_count;
                $categories[] = $cat['name_value_list'];
            }
        }
        return count($categories);
    }

    //Gets all published articles within a defined category
    public function getArticles($id, $offset=0, $limit=10){
        $ann = $this->restClient->getEntry('AOK_Knowledge_Base_Categories', $id, array(), array(array('name' => 'aok_knowledgebase_aok_knowledge_base_categories', 'value' => array('id','name', 'description', 'author','status', 'date_entered', 'date_modified'))));
        foreach($ann['relationship_list'][0] as $rel_mod){
            if($rel_mod['name'] == 'aok_knowledgebase_aok_knowledge_base_categories'){
                $articles = $rel_mod['records'];
            }
        }
        $Articles = array();
        foreach($articles as $article){
            if($article['status']['value'] == 'published_public'){
                $Articles[] = $article;
            }
        }
        if(is_null($offset)){
            $offset=0;
        }
        //Return slices of array according to pagination offset and limit.
        $articles = array_slice($Articles, $offset, $limit);

        return $articles;
    }

    //Gets an individual article
    public function getArticle($id){
        $article = $this->restClient->getEntry('AOK_KnowledgeBase', $id, array('id','name', 'description', 'author','status', 'revision', 'approver', 'date_entered', 'date_modified'),array(array('name' => 'aok_knowledgebase_aok_knowledge_base_categories', 'value' => array('id','name'))));
        return $article;
    }

    public function searchArticles($query, $offset=0, $limit=10){

        $article_list = $this->restClient->getEntryList("AOK_KnowledgeBase","aok_knowledgebase.status = 'published_public' AND (aok_knowledgebase.description LIKE '%".$query."%' OR aok_knowledgebase.name LIKE '%".$query."%')","name",$offset,array('id','name','author','status','date_modified'),"",$limit);

        $articles= array();
        if($article_list !=false){
            foreach($article_list['entry_list'] as $art) {
                $articles[] = $art['name_value_list'];
            }
        }
        $articles['total_count'] = $article_list['total_count'];
        return $articles;
    }


    //returns a count of the published articles in a category
    public function categoryCount($id){
        $ann = $this->restClient->getEntry('AOK_Knowledge_Base_Categories', $id, array(), array(array('name' => 'aok_knowledgebase_aok_knowledge_base_categories', 'value' => array('id','name', 'description','status', 'date_entered'))));
        foreach($ann['relationship_list'][0] as $rel_mod){
            if($rel_mod['name'] == 'aok_knowledgebase_aok_knowledge_base_categories'){
                $articles = $rel_mod['records'];
            }
        }
        $Articles = array();
        foreach($articles as $article){
            if($article['status']['value'] == 'published_public'){
                $Articles[] = $article;
            }
        }
        $articles = count($Articles);

        return $articles;
    }

    public static function isValidPortalUser($user){
        return !empty($user->id) && $user->getParam("sugarid");
    }

    public static function isUserBlocked($user){
        return $user->getParam("aop_block");
    }

}