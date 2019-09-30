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
class IMI_Blog_Block_Posts_Commentsview extends IMI_Blog_Block_Posts_Comments
{
  function getCommentsLimit(){
    $comments = Mage::getModel('imi_blog/postcomment')
      ->getCollection()
      ->addFieldToFilter('post_id',$this->getPosts()->getId())
      ->addFieldToFilter('status',1)
      ->setOrder('entity_id', 'DESC');
    return $comments;
  }
  function viewDetails(){
    return false;
  }
}
