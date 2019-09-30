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
class IMI_Blog_Block_Posts_Comments extends Mage_Core_Block_Template
{
    function getPostComments(){
        $comments = Mage::getModel('imi_blog/postcomment')
            ->getCollection()
            ->addFieldToFilter('post_id',$this->getPosts()->getId())
            ->addFieldToFilter('status',1)
            ->setOrder('entity_id', 'DESC');
        return $comments;
    }
    function getCommentsReplies($commentId){
        $comments = Mage::getModel('imi_blog/reply')
            ->getCollection()
            ->addFieldToFilter('post_id',$this->getPosts()->getId())
            ->addFieldToFilter('comment_id',$commentId)
            ->addFieldToFilter('status',1)
            ->setOrder('entity_id', 'ASC');
        return $comments;
    }
    function getNumberofComments(){
        $comments = $this->getPostComments();
        return count($comments);
    }
    function viewDetails(){
        return true;
    }
    function getCommentsLimit($postId){
        $comments = $this->getPostComments()->setPageSize(Mage::getStoreConfig('imi_blog/posts/countcomments'));
        return $comments;
    }
}
