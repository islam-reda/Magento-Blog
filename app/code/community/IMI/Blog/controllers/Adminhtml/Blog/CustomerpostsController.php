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
 * Posts admin controller
 *
 * @category    IMI
 * @package     IMI_Blog
 * @author      Ultimate Module Creator
 */
class IMI_Blog_Adminhtml_Blog_CustomerpostsController extends IMI_Blog_Controller_Adminhtml_Blog
{
    /**
     * init the posts
     *
     * @access protected
     * @return IMI_Blog_Model_Posts
     */
    public function sendEmailAction()
    {

       $customersIds = $this->getRequest()->getParam('customer');

        $postId = $this->getRequest()->getParam('post_id');

        //zend_debug::dump($postId);

       // die();

        if (!is_array($customersIds)) {
             Mage::getSingleton('adminhtml/session')->addError(
                 Mage::helper('imi_blog')->__('Please select posts to delete.')
             );
         }

         else {
             try {
                 foreach ($customersIds as $customersId) {
                   $customer = Mage::getModel('customer/customer')
                       ->load($customersId);

                     $post = Mage::getModel('imi_blog/posts')
                         ->load($this->getRequest()->getParam('post_id'));

                     $this->sendTransactionalEmailpostsToCustomers(
                         $customer->getName(),
                         $customer->getEmail(),
                         Mage::getBaseUrl().'/'.$post->getUrlKey(),
                         $post->getTitle()
                     );
                 }
             } catch (Mage_Core_Exception $e) {
                 Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
             } catch (Exception $e) {
                 Mage::getSingleton('adminhtml/session')->addError(
                     Mage::helper('imi_blog')->__('There was an error deleting posts.')
                 );
                 Mage::logException($e);
             }
         }
        // $this->_redirect('*/blog_posts/edit/id');
         $this->_redirect('*/blog_posts/edit', array('id'=>$postId));

    }
    public function sendTransactionalEmailpostsToCustomers($customerName,$CustomerEmail,$link,$title)
    {
        if($customerName && $CustomerEmail && $link && $title){
            $templateId = Mage::getStoreConfig('imi_blog/posts/posttemplate');
            // Set sender information
            $senderName = Mage::getStoreConfig('trans_email/ident_support/name');
            $senderEmail = Mage::getStoreConfig('trans_email/ident_support/email');
            $sender = array('name' => $senderName,
                'email' => $senderEmail);
            // Set recepient information
            $recepientEmail = $CustomerEmail;
            $recepientName = $customerName;
            // Get Store ID
            $storeId = Mage::app()->getStore()->getId();
            // Set variables that can be used in email template
            $vars = array(
                'customer_name' => $customerName,
                'title' => $title,
                'link' => $link,
            );
            $translate  = Mage::getSingleton('core/translate');
            // Send Transactional Email
            Mage::getModel('core/email_template')
                ->sendTransactional($templateId, $sender, $recepientEmail, $recepientName, $vars, $storeId);
            $translate->setTranslateInline(true);
            Mage::getSingleton('adminhtml/session')->addSuccess(
                Mage::helper('imi_blog')->__('Email Send successfully')
            );
            return true;
        }
        return false;
    }



    public function sendTransactionalEmailToCustomers($customerName,$CustomerEmail,$reply,$comment)
    {
        if($customerName && $CustomerEmail && $reply && $comment){
            $templateId = Mage::getStoreConfig('imi_blog/posts/emailtemplate');

            // Set sender information
            $senderName = Mage::getStoreConfig('trans_email/ident_support/name');
            $senderEmail = Mage::getStoreConfig('trans_email/ident_support/email');
            $sender = array('name' => $senderName,
                'email' => $senderEmail);

            // Set recepient information
            $recepientEmail = $CustomerEmail;
            $recepientName = $customerName;
            // Get Store ID
            $storeId = Mage::app()->getStore()->getId();

            // Set variables that can be used in email template
            $vars = array(
                'user_name' => $customerName,
                'comment' => $comment,
                'reply' => $reply,
            );
            $translate  = Mage::getSingleton('core/translate');
            // Send Transactional Email
            Mage::getModel('core/email_template')
                ->sendTransactional($templateId, $sender, $recepientEmail, $recepientName, $vars, $storeId);
            $translate->setTranslateInline(true);
            Mage::getSingleton('adminhtml/session')->addSuccess(
                Mage::helper('imi_blog')->__('Email Send successfully')
            );
            return true;
        }
        return false;
    }

    protected function _initPosts()
    {
        $postsId  = (int) $this->getRequest()->getParam('id');
        $posts    = Mage::getModel('imi_blog/posts');
        if ($postsId) {
            $posts->load($postsId);
        }
        Mage::register('current_posts', $posts);
        return $posts;
    }

    /**
     * default action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->_title(Mage::helper('imi_blog')->__('imi'))
             ->_title(Mage::helper('imi_blog')->__('Posts'));
        $this->renderLayout();
    }

    /**
     * grid action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function gridAction()
    {
        $this->loadLayout()->renderLayout();
    }
    public function commentgridAction()
    {
        $this->loadLayout()->renderLayout();
    }
    /**
     * edit posts - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function editAction()
    {
        $postsId    = $this->getRequest()->getParam('id');
        $posts      = $this->_initPosts();
        if ($postsId && !$posts->getId()) {
            $this->_getSession()->addError(
                Mage::helper('imi_blog')->__('This posts no longer exists.')
            );
            $this->_redirect('*/*/');
            return;
        }
        $data = Mage::getSingleton('adminhtml/session')->getPostsData(true);
        if (!empty($data)) {
            $posts->setData($data);
        }
        Mage::register('posts_data', $posts);
        $this->loadLayout();
        $this->_title(Mage::helper('imi_blog')->__('imi'))
             ->_title(Mage::helper('imi_blog')->__('Posts'));
        if ($posts->getId()) {
            $this->_title($posts->getTitle());
        } else {
            $this->_title(Mage::helper('imi_blog')->__('Add posts'));
        }
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
        $this->renderLayout();
    }

    /**
     * new posts action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * save posts - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
     public function sendTransactionalEmail($customerName,$CustomerEmail,$reply,$comment)
     {
          if($customerName && $CustomerEmail && $reply && $comment){
              $templateId = Mage::getStoreConfig('imi_blog/posts/emailtemplate');

              // Set sender information
              $senderName = Mage::getStoreConfig('trans_email/ident_support/name');
              $senderEmail = Mage::getStoreConfig('trans_email/ident_support/email');
              $sender = array('name' => $senderName,
                          'email' => $senderEmail);

              // Set recepient information
              $recepientEmail = $CustomerEmail;
              $recepientName = $customerName;
              // Get Store ID
              $storeId = Mage::app()->getStore()->getId();

              // Set variables that can be used in email template
              $vars = array(
                  'user_name' => $customerName,
                  'comment' => $comment,
                  'reply' => $reply,
              );
              $translate  = Mage::getSingleton('core/translate');
              // Send Transactional Email
              Mage::getModel('core/email_template')
                  ->sendTransactional($templateId, $sender, $recepientEmail, $recepientName, $vars, $storeId);
              $translate->setTranslateInline(true);
              Mage::getSingleton('adminhtml/session')->addSuccess(
                  Mage::helper('imi_blog')->__('Email Send successfully')
              );
              return true;
          }
          return false;
      }

     public function saveAction()
     {
         if ($data = $this->getRequest()->getPost('posts')) {

             try {
                 $user = Mage::getSingleton('admin/session');
                 $userId = $user->getUser()->getUserId();
                 $userUsername = $user->getUser()->getUsername();
                 $customerData = array(
                   'post_id' => $data['post_id'],
                   'comment' => $data['comment'],
                   'repond_id' => $userId,
                   'repond_name' => $userUsername,
                   'is_admin' => 1,
                   'status' => 1,
                 );
                 $reply = Mage::getModel('imi_blog/postcomment')->addData($customerData)->save();
                 // send transaction email
                 if(Mage::getStoreConfig('imi_blog/posts/sendemail')){
                   $comment = Mage::getModel('imi_blog/postcomment')
                      ->getCollection()
                      ->addFieldToFilter('post_id',$data['post_id'])
                      ->addFieldToFilter('is_admin',0)
                      ->getLastItem();
                   if($comment && $comment->getId() && !$comment->getIsAdmin()){
                       $customerId = $comment->getRepondId();
                       $customerName = $comment->getRepondName();
                       $customer = Mage::getModel('customer/customer')->load($customerId);
                       $this->sendTransactionalEmail($customerName,$customer->getEmail(),$reply->getComment(),$comment->getComment());
                   }else{
                     $post = Mage::getModel('imi_blog/posts')->load($data['post_id']);
                     $customerId = $post->getPostedById();
                     $customer = Mage::getModel('customer/customer')->load($customerId);
                     $this->sendTransactionalEmail($customer->getName(),$customer->getEmail(),$reply->getComment(),$post->getTitle());
                   }
                 }
                 Mage::getSingleton('adminhtml/session')->addSuccess(
                     Mage::helper('imi_blog')->__('Comment was successfully saved')
                 );
                 Mage::getSingleton('adminhtml/session')->setFormData(false);
                 if ($this->getRequest()->getParam('back')) {
                     $this->_redirect('*/*/edit', array('id' => $data['post_id']));
                     return;
                 }
                 $this->_redirect('*/*/');
                 return;
             } catch (Mage_Core_Exception $e) {
                 if (isset($data['image']['value'])) {
                     $data['image'] = $data['image']['value'];
                 }
                 Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                 Mage::getSingleton('adminhtml/session')->setPostsData($data);
                 $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                 return;
             } catch (Exception $e) {
                 Mage::logException($e);
                 if (isset($data['image']['value'])) {
                     $data['image'] = $data['image']['value'];
                 }
                 Mage::getSingleton('adminhtml/session')->addError(
                     Mage::helper('imi_blog')->__('There was a problem saving the comment.')
                 );
                 Mage::getSingleton('adminhtml/session')->setPostsData($data);
                 $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                 return;
             }
         }

         Mage::getSingleton('adminhtml/session')->addError(
             Mage::helper('imi_blog')->__('Unable to find comment to save.')
         );

         $this->_redirect('*/*/');
     }


    /**
     * delete posts - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
     public function deleteAction()
     {
         if ( $this->getRequest()->getParam('id') > 0) {
             try {
                 $comments = Mage::getModel('imi_blog/postcomment')
                   ->getCollection()
                   ->addFieldToFilter('post_id',$this->getRequest()->getParam('id'));
                 foreach ($comments as $key => $comment) {
                   $replies = Mage::getModel('imi_blog/reply')
                     ->getCollection()
                     ->addFieldToFilter('comment_id',$comment->getId())
                     ->addFieldToFilter('post_id',$this->getRequest()->getParam('id'));
                   foreach ($replies as $key => $reply) {
                     $reply->delete();
                   }
                   $comment->delete();
                 }
                 $posts = Mage::getModel('imi_blog/posts');
                 $posts->setId($this->getRequest()->getParam('id'))->delete();

                 Mage::getSingleton('adminhtml/session')->addSuccess(
                     Mage::helper('imi_blog')->__('Posts was successfully deleted.')
                 );
                 $this->_redirect('*/*/');
                 return;
             } catch (Mage_Core_Exception $e) {
                 Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                 $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
             } catch (Exception $e) {
                 Mage::getSingleton('adminhtml/session')->addError(
                     Mage::helper('imi_blog')->__('There was an error deleting posts.')
                 );
                 $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                 Mage::logException($e);
                 return;
             }
         }
         Mage::getSingleton('adminhtml/session')->addError(
             Mage::helper('imi_blog')->__('Could not find posts to delete.')
         );
         $this->_redirect('*/*/');
     }

    /**
     * mass delete posts - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */

     public function categoriesAction()
     {
         $this->loadLayout();
         $this->renderLayout();
     }

     public function massDeleteAction()
     {
         $postsIds = $this->getRequest()->getParam('posts');
         if (!is_array($postsIds)) {
             Mage::getSingleton('adminhtml/session')->addError(
                 Mage::helper('imi_blog')->__('Please select posts to delete.')
             );
         } else {
             try {
                 foreach ($postsIds as $postsId) {
                     $comments = Mage::getModel('imi_blog/postcomment')
                       ->getCollection()
                       ->addFieldToFilter('post_id',$postsId);
                     foreach ($comments as $key => $comment) {
                       $replies = Mage::getModel('imi_blog/reply')
                         ->getCollection()
                         ->addFieldToFilter('comment_id',$comment->getId())
                         ->addFieldToFilter('post_id',$postsId);
                       foreach ($replies as $key => $reply) {
                         $reply->delete();
                       }
                       $comment->delete();
                     }
                     $posts = Mage::getModel('imi_blog/posts');
                     $posts->setId($postsId)->delete();
                 }
                 Mage::getSingleton('adminhtml/session')->addSuccess(
                     Mage::helper('imi_blog')->__('Total of %d posts were successfully deleted.', count($postsIds))
                 );
             } catch (Mage_Core_Exception $e) {
                 Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
             } catch (Exception $e) {
                 Mage::getSingleton('adminhtml/session')->addError(
                     Mage::helper('imi_blog')->__('There was an error deleting posts.')
                 );
                 Mage::logException($e);
             }
         }
         $this->_redirect('*/*/index');
     }

    /**
     * mass status change - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function massStatusAction()
    {
        $postsIds = $this->getRequest()->getParam('posts');
        if (!is_array($postsIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('imi_blog')->__('Please select posts.')
            );
        } else {
            try {
                foreach ($postsIds as $postsId) {
                $posts = Mage::getSingleton('imi_blog/posts')->load($postsId)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d posts were successfully updated.', count($postsIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('imi_blog')->__('There was an error updating posts.')
                );
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massStatusPostCommentsAction()
    {
        $postsIds = $this->getRequest()->getParam('posts');

        if (!is_array($postsIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('imi_blog')->__('Please select posts.')
            );
        } else {
            try {


                foreach ($postsIds as $postsId) {


                    $posts = Mage::getSingleton('imi_blog/postcomment')->load($postsId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d posts were successfully updated.', count($postsIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('imi_blog')->__('There was an error updating posts.')
                );
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * mass is admin change - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function massIsAdminAction()
    {
        $postsIds = $this->getRequest()->getParam('posts');
        if (!is_array($postsIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('imi_blog')->__('Please select posts.')
            );
        } else {
            try {
                foreach ($postsIds as $postsId) {
                    $posts = Mage::getModel('imi_blog/posts')->load($postsId)
                        ->setIsAdmin($this->getRequest()->getParam('flag_is_admin'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d posts were successfully updated.', count($postsIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('imi_blog')->__('There was an error updating posts.')
                );
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * export as csv - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function exportCsvAction()
    {
        $fileName   = 'posts.csv';
        $content    = $this->getLayout()->createBlock('imi_blog/adminhtml_posts_grid')
            ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export as MsExcel - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function exportExcelAction()
    {
        $fileName   = 'posts.xls';
        $content    = $this->getLayout()->createBlock('imi_blog/adminhtml_posts_grid')
            ->getExcelFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export as xml - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function exportXmlAction()
    {
        $fileName   = 'posts.xml';
        $content    = $this->getLayout()->createBlock('imi_blog/adminhtml_posts_grid')
            ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export as csv - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function exportCsvCommentsAction()
    {
        $fileName   = 'comments.csv';
        $content    = $this->getLayout()->createBlock('imi_blog/adminhtml_posts_edit_tab_grid')
            ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export as MsExcel - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function exportExcelCommentsAction()
    {
        $fileName   = 'comments.xls';
        $content    = $this->getLayout()->createBlock('imi_blog/adminhtml_posts_edit_tab_grid')
            ->getExcelFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export as xml - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function exportXmlActionCommentsAction()
    {
        $fileName   = 'comments.xml';
        $content    = $this->getLayout()->createBlock('imi_blog/adminhtml_posts_edit_tab_grid')
            ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Check if admin has permissions to visit related pages
     *
     * @access protected
     * @return boolean
     * @author Ultimate Module Creator
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('imi_loyalty/imi_blog/posts');
    }
}
