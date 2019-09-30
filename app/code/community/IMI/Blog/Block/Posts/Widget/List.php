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
 * Posts widget block
 *
 * @category    IMI
 * @package     IMI_Blog
 * @author      Ultimate Module Creator
 */
class IMI_Blog_Block_Posts_Widget_List extends IMI_Blog_Block_Posts_List implements
    Mage_Widget_Block_Interface
{
    protected $_htmlTemplate = 'imi_blog/posts/widget/list.phtml';

    /**
     * Prepare a for widget
     *
     * @access protected
     * @return IMI_Blog_Block_Posts_Widget_View
     * @author Ultimate Module Creator
     */
    protected function _beforeToHtml()
    {
        parent::_beforeToHtml();
        $count = $this->getData('count');
        if ($count) {
            $postss = Mage::getResourceModel('imi_blog/posts_collection')
                             ->addStoreFilter(Mage::app()->getStore())
                             ->addFieldToFilter('status', 1)
                             ->setPageSize($count);
            $this->setCurrentPosts($postss);
            $this->setTemplate($this->_htmlTemplate);
        }
        return $this;
    }
}
