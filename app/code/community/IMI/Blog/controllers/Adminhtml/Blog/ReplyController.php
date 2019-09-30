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
 * Reply admin controller
 *
 * @category    IMI
 * @package     IMI_Blog
 * @author      Ultimate Module Creator
 */
class IMI_Blog_Adminhtml_Blog_ReplyController extends IMI_Blog_Controller_Adminhtml_Blog
{
    /**
     * init the reply
     *
     * @access protected
     * @return IMI_Blog_Model_Reply
     */
    protected function _initReply()
    {
        $replyId  = (int) $this->getRequest()->getParam('id');
        $reply    = Mage::getModel('imi_blog/reply');
        if ($replyId) {
            $reply->load($replyId);
        }
        Mage::register('current_reply', $reply);
        return $reply;
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
             ->_title(Mage::helper('imi_blog')->__('Replies'));
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

    /**
     * edit reply - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function editAction()
    {
        $replyId    = $this->getRequest()->getParam('id');
        $reply      = $this->_initReply();
        if ($replyId && !$reply->getId()) {
            $this->_getSession()->addError(
                Mage::helper('imi_blog')->__('This reply no longer exists.')
            );
            $this->_redirect('*/*/');
            return;
        }
        $data = Mage::getSingleton('adminhtml/session')->getReplyData(true);
        if (!empty($data)) {
            $reply->setData($data);
        }
        Mage::register('reply_data', $reply);
        $this->loadLayout();
        $this->_title(Mage::helper('imi_blog')->__('imi'))
             ->_title(Mage::helper('imi_blog')->__('Replies'));
        if ($reply->getId()) {
            $this->_title($reply->getPostId());
        } else {
            $this->_title(Mage::helper('imi_blog')->__('Add reply'));
        }
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
        $this->renderLayout();
    }

    /**
     * new reply action
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
     * save reply - action
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
        if ($data = $this->getRequest()->getPost('reply')) {
            try {
                $reply = $this->_initReply();
                $user = Mage::getSingleton('admin/session');
                $userId = $user->getUser()->getUserId();
                $data['status'] = 1;
                $data['user_id'] = $userId;
                $comment =  Mage::getModel('imi_blog/postcomment')->load($data['comment_id']);
                $data['post_id'] = $comment->getPostId();
                $reply->addData($data);
                $reply->save();
                //send transaction email
                if(Mage::getStoreConfig('imi_blog/posts/sendemail')){
                  if(!$comment->getIsAdmin()){
                    $customerId = $comment->getRepondId();
                    $customerName = $comment->getRepondName();
                    $customer = Mage::getModel('customer/customer')->load($customerId);
                    $this->sendTransactionalEmail($customerName,$customer->getEmail(),$reply->getReply(),$comment->getComment());
                  }
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('imi_blog')->__('Reply was successfully saved')
                );
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/blog_posts/edit', array('id' => $reply->getPostId()));
                    return;
                }
                $this->_redirect('*/blog_posts/index');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setReplyData($data);
                $this->_redirect('*/blog_posts/edit', array('id' => $data['post_id']));
                return;
            } catch (Exception $e) {
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('imi_blog')->__('There was a problem saving the reply.')
                );
                Mage::getSingleton('adminhtml/session')->setReplyData($data);
                $this->_redirect('*/blog_posts/edit', array('id' => $data['post_id']));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('imi_blog')->__('Unable to find reply to save.')
        );
        $this->_redirect('*/blog_posts/index');
    }

    /**
     * delete reply - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function deleteAction()
    {
        if ( $this->getRequest()->getParam('id') > 0) {
            try {
                $reply = Mage::getModel('imi_blog/reply');
                $reply->setId($this->getRequest()->getParam('id'))->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('imi_blog')->__('Reply was successfully deleted.')
                );
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('imi_blog')->__('There was an error deleting reply.')
                );
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                Mage::logException($e);
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('imi_blog')->__('Could not find reply to delete.')
        );
        $this->_redirect('*/*/');
    }

    /**
     * mass delete reply - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function massDeleteAction()
    {
        $replyIds = $this->getRequest()->getParam('reply');
        if (!is_array($replyIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('imi_blog')->__('Please select replies to delete.')
            );
        } else {
            try {
                foreach ($replyIds as $replyId) {
                    $reply = Mage::getModel('imi_blog/reply');
                    $reply->setId($replyId)->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('imi_blog')->__('Total of %d replies were successfully deleted.', count($replyIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('imi_blog')->__('There was an error deleting replies.')
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
        $replyIds = $this->getRequest()->getParam('reply');
        if (!is_array($replyIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('imi_blog')->__('Please select replies.')
            );
        } else {
            try {
                foreach ($replyIds as $replyId) {
                $reply = Mage::getSingleton('imi_blog/reply')->load($replyId)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d replies were successfully updated.', count($replyIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('imi_blog')->__('There was an error updating replies.')
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
        $fileName   = 'reply.csv';
        $content    = $this->getLayout()->createBlock('imi_blog/adminhtml_reply_grid')
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
        $fileName   = 'reply.xls';
        $content    = $this->getLayout()->createBlock('imi_blog/adminhtml_reply_grid')
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
        $fileName   = 'reply.xml';
        $content    = $this->getLayout()->createBlock('imi_blog/adminhtml_reply_grid')
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
        return Mage::getSingleton('admin/session')->isAllowed('imi_loyalty/imi_blog/reply');
    }
}
