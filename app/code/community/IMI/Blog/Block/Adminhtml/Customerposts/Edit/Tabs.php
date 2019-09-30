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
 * Posts admin edit tabs
 *
 * @category    IMI
 * @package     IMI_Blog
 * @author      Ultimate Module Creator
 */
class IMI_Blog_Block_Adminhtml_Customerposts_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    /**
     * Initialize Tabs
     *
     * @access public
     * @author Ultimate Module Creator
     */
    public function __construct()
    {

        parent::__construct();

        $this->setId('posts_tabs');

        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('imi_blog')->__('Posts'));

    }



    /**
     * before render html
     *
     * @access protected
     * @return IMI_Blog_Block_Adminhtml_Posts_Edit_Tabs
     * @author Ultimate Module Creator
     */



    protected function _beforeToHtml()
    {
        $this->addTab(
            'form_posts',
            array(
                'label'   => Mage::helper('imi_blog')->__('Posts'),
                'title'   => Mage::helper('imi_blog')->__('Posts'),
                'content' => $this->getLayout()->createBlock(
                    'imi_blog/adminhtml_customerposts_edit_tab_form'
                )->toHtml().$this->getLayout()->createBlock(
                    'imi_blog/adminhtml_customerposts_edit_tab_comments'
                )->setTemplate('imi_blog/posts/post.phtml')->toHtml()
                ,
            )
        );
        // $this->addTab('categories', array(
        //   'label'     => Mage::helper('imi_blog')->__('Categories'),
        //   'url'       => $this->getUrl('*/*/categories', array('_current' => true)),
        //   'class'     => 'ajax',
        // ));
        $this->addTab(
            'comments_tap',
            array(
                'label'   => Mage::helper('imi_blog')->__('Comments'),
                'title'   => Mage::helper('imi_blog')->__('Comments'),
                'content' => $this->getLayout()->createBlock(
                    'imi_blog/adminhtml_customerposts_edit_tab_grid'
                )->toHtml(),
            )
        );


        if (!Mage::app()->isSingleStoreMode()) {
            $this->addTab(
                'form_store_posts',
                array(
                    'label'   => Mage::helper('imi_blog')->__('Store views'),
                    'title'   => Mage::helper('imi_blog')->__('Store views'),
                    'content' => $this->getLayout()->createBlock(
                        'imi_blog/adminhtml_customerposts_edit_tab_stores'
                    )
                    ->toHtml(),
                )
            );
        }
        return parent::_beforeToHtml();
    }

    /**
     * Retrieve posts entity
     *
     * @access public
     * @return IMI_Blog_Model_Posts
     * @author Ultimate Module Creator
     */
    public function getPosts()
    {
        return Mage::registry('current_posts');
    }
}
