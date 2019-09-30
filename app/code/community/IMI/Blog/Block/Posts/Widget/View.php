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
class IMI_Blog_Block_Posts_Widget_View extends IMI_Blog_Block_Posts_List implements
    Mage_Widget_Block_Interface
{
    protected $_htmlTemplate = 'imi_blog/posts/widget/view.phtml';

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
        $postsId = $this->getData('posts_id');
        if ($postsId) {
            $posts = Mage::getModel('imi_blog/posts')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($postsId);
            if ($posts->getStatus()) {
                $this->setCurrentPosts($posts);
                $this->setTemplate($this->_htmlTemplate);
            }
        }
        return $this;
    }
}
