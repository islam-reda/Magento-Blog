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
 * Comment admin controller
 *
 * @category    IMI
 * @package     IMI_Blog
 * @author      Ultimate Module Creator
 */
class IMI_Blog_Adminhtml_Blog_PostcommentController extends IMI_Blog_Controller_Adminhtml_Blog
{
    /**
     * init the comment
     *
     * @access protected
     * @return IMI_Blog_Model_Postcomment
     */
    protected function _initPostcomment()
    {
        $postcommentId  = (int) $this->getRequest()->getParam('id');
        $postcomment    = Mage::getModel('imi_blog/postcomment');
        if ($postcommentId) {
            $postcomment->load($postcommentId);
        }
        Mage::register('current_postcomment', $postcomment);
        return $postcomment;
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
             ->_title(Mage::helper('imi_blog')->__('Comments'));
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
     * edit comment - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function editAction()
    {
        $postcommentId    = $this->getRequest()->getParam('id');
        $postcomment      = $this->_initPostcomment();
        if ($postcommentId && !$postcomment->getId()) {
            $this->_getSession()->addError(
                Mage::helper('imi_blog')->__('This comment no longer exists.')
            );
            $this->_redirect('*/*/');
            return;
        }
        $data = Mage::getSingleton('adminhtml/session')->getPostcommentData(true);
        if (!empty($data)) {
            $postcomment->setData($data);
        }
        Mage::register('postcomment_data', $postcomment);
        $this->loadLayout();
        $this->_title(Mage::helper('imi_blog')->__('imi'))
             ->_title(Mage::helper('imi_blog')->__('Comments'));
        if ($postcomment->getId()) {
            $this->_title($postcomment->getPostId());
        } else {
            $this->_title(Mage::helper('imi_blog')->__('Add comment'));
        }
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
        $this->renderLayout();
    }

    /**
     * new comment action
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
     * save comment - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost('postcomment')) {
            try {
                $postcomment = $this->_initPostcomment();
                $postcomment->addData($data);
                $postcomment->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('imi_blog')->__('Comment was successfully saved')
                );
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $postcomment->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setPostcommentData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            } catch (Exception $e) {
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('imi_blog')->__('There was a problem saving the comment.')
                );
                Mage::getSingleton('adminhtml/session')->setPostcommentData($data);
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
     * delete comment - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function deleteAction()
    {
        if ( $this->getRequest()->getParam('id') > 0) {
            try {
                $postcomment = Mage::getModel('imi_blog/postcomment');
                $replies = Mage::getModel('imi_blog/reply')
                  ->getCollection()
                  ->addFieldToFilter('comment_id',$this->getRequest()->getParam('id'));
                foreach ($replies as $key => $reply) {
                  $reply->delete();
                }
                $postcomment->setId($this->getRequest()->getParam('id'))->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('imi_blog')->__('Comment was successfully deleted.')
                );
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('imi_blog')->__('There was an error deleting comment.')
                );
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                Mage::logException($e);
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('imi_blog')->__('Could not find comment to delete.')
        );
        $this->_redirect('*/*/');
    }

    /**
     * mass delete comment - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */

     public function massDeletelogAction()
     {
       $postcommentIds = $this->getRequest()->getParam('postcomment');
       if (!is_array($postcommentIds)) {
           Mage::getSingleton('adminhtml/session')->addError(
               Mage::helper('imi_blog')->__('Please select comments to delete.')
           );
       }
       else {
           try {
               $postId = '';
               foreach ($postcommentIds as $postcommentId) {
                   $replies = Mage::getModel('imi_blog/reply')
                     ->getCollection()
                     ->addFieldToFilter('comment_id',$postcommentId);
                   foreach ($replies as $key => $reply) {
                     $reply->delete();
                   }
                   $postcomment = Mage::getModel('imi_blog/postcomment');
                   $postcomment->setId($postcommentId)->delete();
               }

               Mage::getSingleton('adminhtml/session')->addSuccess(
                   Mage::helper('imi_blog')->__(
                       'Total of %d comments were successfully deleted.',
                       count($postcommentIds))
               );
           } catch (Mage_Core_Exception $e) {
               Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
           } catch (Exception $e) {
               Mage::getSingleton('adminhtml/session')->addError(
                   Mage::helper('imi_blog')->__('There was an error deleting comments.')
               );
               Mage::logException($e);
           }
       }
       $this->_redirect('*/*/index');
     }
     public function customermassDeleteAction()
     {
         $postcommentIds = $this->getRequest()->getParam('postcomment');

         if (!is_array($postcommentIds)) {

             Mage::getSingleton('adminhtml/session')->addError(
                 Mage::helper('imi_blog')->__('Please select comments to delete.')
             );
         }


         else {
             try {
                 $postId = '';

                 foreach ($postcommentIds as $postcommentId) {

                     $postcomment = Mage::getModel('imi_blog/postcomment');

                     if(!$postId){

                         $postId = Mage::getModel('imi_blog/postcomment')->load($postcommentId)->getPostId();

                     }

                     $postcomment->setId($postcommentId)->delete();

                     //$CommentId = $postcommentId;
                 }

                 Mage::getSingleton('adminhtml/session')->addSuccess(
                     Mage::helper('imi_blog')->__(
                         'Total of %d comments were successfully deleted.',
                         count($postcommentIds))
                 );
             } catch (Mage_Core_Exception $e) {
                 Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
             } catch (Exception $e) {
                 Mage::getSingleton('adminhtml/session')->addError(
                     Mage::helper('imi_blog')->__('There was an error deleting comments.')
                 );
                 Mage::logException($e);
             }
         }

         $this->_redirect('*/blog_customerposts/edit', array('id' => $postId));
     }
    public function massDeleteAction()
    {
        $postcommentIds = $this->getRequest()->getParam('postcomment');

        if (!is_array($postcommentIds)) {

            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('imi_blog')->__('Please select comments to delete.')
            );
        }


        else {
            try {
                $postId = '';

                foreach ($postcommentIds as $postcommentId) {

                    $postcomment = Mage::getModel('imi_blog/postcomment');
                    $replies = Mage::getModel('imi_blog/reply')
                      ->getCollection()
                      ->addFieldToFilter('comment_id',$postcommentId);
                    foreach ($replies as $key => $reply) {
                      $reply->delete();
                    }
                    if(!$postId){

                        $postId = Mage::getModel('imi_blog/postcomment')->load($postcommentId)->getPostId();

                    }

                    $postcomment->setId($postcommentId)->delete();

                    //$CommentId = $postcommentId;
                }

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('imi_blog')->__(
                        'Total of %d comments were successfully deleted.',
                        count($postcommentIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('imi_blog')->__('There was an error deleting comments.')
                );
                Mage::logException($e);
            }
        }

        $this->_redirect('*/blog_posts/edit', array('id' => $postId));
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
        $postcommentIds = $this->getRequest()->getParam('postcomment');
        if (!is_array($postcommentIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('imi_blog')->__('Please select comments.')
            );
        } else {
            try {
                foreach ($postcommentIds as $postcommentId) {
                $postcomment = Mage::getSingleton('imi_blog/postcomment')->load($postcommentId)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d comments were successfully updated.', count($postcommentIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('imi_blog')->__('There was an error updating comments.')
                );
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }
    public function customermassStatusPostCommentsAction(){
      $id = $this->getRequest()->getParam('id');


      // $postcommentIds = $this->getRequest()->getParam('postcomment');

      $postcommentIds = $this->getRequest()->getParam('postcomment');
      if (!is_array($postcommentIds)) {
          Mage::getSingleton('adminhtml/session')->addError(
              Mage::helper('imi_blog')->__('Please select comments.')
          );
      } else {
          try {
              foreach ($postcommentIds as $postcommentId) {
                  $postcomment = Mage::getSingleton('imi_blog/postcomment')->load($postcommentId)
                      ->setStatus($this->getRequest()->getParam('status'))
                      ->setIsMassupdate(true)
                      ->save();
              }
              $this->_getSession()->addSuccess(
                  $this->__('Total of %d comments were successfully updated.', count($postcommentIds))
              );
          } catch (Mage_Core_Exception $e) {
              Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
          } catch (Exception $e) {
              Mage::getSingleton('adminhtml/session')->addError(
                  Mage::helper('imi_blog')->__('There was an error updating comments.')
              );
              Mage::logException($e);
          }
      }
      $this->_redirect("*/blog_customerposts/edit/id/$id");
    }
    public function massStatusPostCommentsAction()
    {
        $id = $this->getRequest()->getParam('id');


        // $postcommentIds = $this->getRequest()->getParam('postcomment');

        $postcommentIds = $this->getRequest()->getParam('postcomment');
        if (!is_array($postcommentIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('imi_blog')->__('Please select comments.')
            );
        } else {
            try {
                foreach ($postcommentIds as $postcommentId) {
                    $postcomment = Mage::getSingleton('imi_blog/postcomment')->load($postcommentId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d comments were successfully updated.', count($postcommentIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('imi_blog')->__('There was an error updating comments.')
                );
                Mage::logException($e);
            }
        }
        $this->_redirect("*/blog_posts/edit/id/$id");
    }

    /**
     * mass Is admin change - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function massIsAdminAction()
    {

        $postcommentIds = $this->getRequest()->getParam('postcomment');
        if (!is_array($postcommentIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('imi_blog')->__('Please select comments.')
            );
        }

        else {

            try {
                foreach ($postcommentIds as $postcommentId) {
                    $postcomment = Mage::getModel('imi_blog/postcomment')->load($postcommentId)
                        ->setIsAdmin($this->getRequest()->getParam('flag_is_admin'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d comments were successfully updated.', count($postcommentIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('imi_blog')->__('There was an error updating comments.')
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
        $fileName   = 'postcomment.csv';
        $content    = $this->getLayout()->createBlock('imi_blog/adminhtml_postcomment_grid')
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
        $fileName   = 'postcomment.xls';
        $content    = $this->getLayout()->createBlock('imi_blog/adminhtml_postcomment_grid')
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
        $fileName   = 'postcomment.xml';
        $content    = $this->getLayout()->createBlock('imi_blog/adminhtml_postcomment_grid')
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
        return Mage::getSingleton('admin/session')->isAllowed('imi_loyalty/imi_blog/postcomment');
    }
}
