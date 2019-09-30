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
 * Category admin edit tabs
 *
 * @category    IMI
 * @package     IMI_Blog
 * @author      Ultimate Module Creator
 */
class IMI_Blog_Block_Adminhtml_Category_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     * Initialize Tabs
     *
     * @access public
     * @author Ultimate Module Creator
     */
    public function __construct()
    {
        $this->setId('category_info_tabs');
        $this->setDestElementId('category_tab_content');
        $this->setTitle(Mage::helper('imi_blog')->__('Category'));
        $this->setTemplate('widget/tabshoriz.phtml');
    }

    /**
     * Prepare Layout Content
     *
     * @access public
     * @return IMI_Blog_Block_Adminhtml_Category_Edit_Tabs
     */
    protected function _prepareLayout()
    {
        $this->addTab(
            'form_category',
            array(
                'label'   => Mage::helper('imi_blog')->__('Category'),
                'title'   => Mage::helper('imi_blog')->__('Category'),
                'content' => $this->getLayout()->createBlock(
                    'imi_blog/adminhtml_category_edit_tab_form'
                )
                ->toHtml(),
            )
        );
        if (!Mage::app()->isSingleStoreMode()) {
            $this->addTab(
                'form_store_category',
                array(
                    'label'   => Mage::helper('imi_blog')->__('Store views'),
                    'title'   => Mage::helper('imi_blog')->__('Store views'),
                    'content' => $this->getLayout()->createBlock(
                        'imi_blog/adminhtml_category_edit_tab_stores'
                    )
                    ->toHtml(),
                )
            );
        }
        return parent::_beforeToHtml();
    }

    /**
     * Retrieve category entity
     *
     * @access public
     * @return IMI_Blog_Model_Category
     * @author Ultimate Module Creator
     */
    public function getCategory()
    {
        return Mage::registry('current_category');
    }
}
