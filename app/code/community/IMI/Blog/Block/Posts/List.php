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
 * Posts list block
 *
 * @category    IMI
 * @package     IMI_Blog
 * @author      Ultimate Module Creator
 */
class IMI_Blog_Block_Posts_List extends Mage_Core_Block_Template
{
    /**
     * initialize
     *
     * @access public
     * @author Ultimate Module Creator
     */
    public function _construct()
    {
        parent::_construct();
        $postss = Mage::getResourceModel('imi_blog/posts_collection')
                         ->addStoreFilter(Mage::app()->getStore())
                         ->addFieldToFilter('is_admin', 1)
                         ->addFieldToFilter('status', 1);
        $category_id = $this->getRequest()->getParam('category_id');
        if($category_id){
          $postss->addFieldToFilter('category_id', $category_id);
        }
        $postss->setOrder('title', 'asc');
        $this->setPostss($postss);
    }
    public function getCategoryName($category_id){
      $category = Mage::getModel('imi_blog/category')->load($category_id);
      if($category && $category->getId()){
          return $category;
      }else{
        return array();
      }
    }
    public function getAjaxLinks(){
      return array(
        'addcomment' => Mage::getUrl('imi_blog/posts/addcomment'),
        'viewcomment' => Mage::getUrl('imi_blog/posts/viewcomment'),
      );
    }

    /**
     * prepare the layout
     *
     * @access protected
     * @return IMI_Blog_Block_Posts_List
     * @author Ultimate Module Creator
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $pager = $this->getLayout()->createBlock(
            'page/html_pager',
            'imi_blog.posts.html.pager'
        )
        ->setCollection($this->getPostss());
        $this->setChild('pager', $pager);
        $this->getPostss()->load();
        return $this;
    }

    protected function recentposts()
    {
        $posts = Mage::getModel('imi_blog/posts')->getCollection()
                ->addStoreFilter(Mage::app()->getStore())
                ->addFieldToFilter('status', 1)
                ->addFieldToFilter('is_admin', 1)
                ->setOrder('created_at', 'desc')
                ->setPageSize(4);
        return $posts;
    }


    /**
     * get the pager html
     *
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
}
