<?php
/**
 * Created by PhpStorm.
 * User: it
 * Date: 18/05/22
 * Time: 11:41 AM
 */

class IMI_Blog_Block_Inqueries extends IMI_Blog_Block_Posts_List
{

  public function getmyposts()
  {
      $customerData = Mage::getSingleton('customer/session')->getCustomer();
      $posts = Mage::getModel('imi_blog/posts')
                       ->getCollection()
                       ->addFieldToFilter('is_admin', 0)
                       ->addFieldToFilter('posted_by_id', $customerData->getId())
                       ->addFieldToFilter('status', 1)
                       ->setOrder('entity_id', 'desc');
      return $posts;
  }

}
