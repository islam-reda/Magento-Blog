<?php
/**
 * IMI_Blog extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       IMI
 * @package        IMI_Blog
 * @copyright      Copyright (c) 2018
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Admin search model
 *
 * @category    IMI
 * @package     IMI_Blog
 * @author      Ultimate Module Creator
 */
class IMI_Blog_Model_Adminhtml_Search_Posts extends Varien_Object
{
    /**
     * Load search results
     *
     * @access public
     * @return IMI_Blog_Model_Adminhtml_Search_Posts
     * @author Ultimate Module Creator
     */
    public function load()
    {
        $arr = array();
        if (!$this->hasStart() || !$this->hasLimit() || !$this->hasQuery()) {
            $this->setResults($arr);
            return $this;
        }
        $collection = Mage::getResourceModel('imi_blog/posts_collection')
            ->addFieldToFilter('title', array('like' => $this->getQuery().'%'))
            ->setCurPage($this->getStart())
            ->setPageSize($this->getLimit())
            ->load();
        foreach ($collection->getItems() as $posts) {
            $arr[] = array(
                'id'          => 'posts/1/'.$posts->getId(),
                'type'        => Mage::helper('imi_blog')->__('Posts'),
                'name'        => $posts->getTitle(),
                'description' => $posts->getTitle(),
                'url' => Mage::helper('adminhtml')->getUrl(
                    '*/blog_posts/edit',
                    array('id'=>$posts->getId())
                ),
            );
        }
        $this->setResults($arr);
        return $this;
    }
}
