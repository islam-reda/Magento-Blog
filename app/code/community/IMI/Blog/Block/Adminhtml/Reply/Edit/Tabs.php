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
 * Reply admin edit tabs
 *
 * @category    IMI
 * @package     IMI_Blog
 * @author      Ultimate Module Creator
 */
class IMI_Blog_Block_Adminhtml_Reply_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
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
        $this->setId('reply_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('imi_blog')->__('Reply'));
    }

    /**
     * before render html
     *
     * @access protected
     * @return IMI_Blog_Block_Adminhtml_Reply_Edit_Tabs
     * @author Ultimate Module Creator
     */
    protected function _beforeToHtml()
    {
        $this->addTab(
            'form_reply',
            array(
                'label'   => Mage::helper('imi_blog')->__('Reply'),
                'title'   => Mage::helper('imi_blog')->__('Reply'),
                'content' => $this->getLayout()->createBlock(
                    'imi_blog/adminhtml_reply_edit_tab_form'
                )
                ->toHtml(),
            )
        );
        return parent::_beforeToHtml();
    }

    /**
     * Retrieve reply entity
     *
     * @access public
     * @return IMI_Blog_Model_Reply
     * @author Ultimate Module Creator
     */
    public function getReply()
    {
        return Mage::registry('current_reply');
    }
}
