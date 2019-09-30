<?php
/**
 * Created by PhpStorm.
 * User: it
 * Date: 18/05/22
 * Time: 11:26 AM
 */

class IMI_Blog_InqueriesController extends Mage_Core_Controller_Front_Action
{
    public function  addAction(){
        if(!Mage::helper('customer')->isLoggedIn()){
          $this->_redirect('customer/account/');
        }
        $this->loadLayout();
        $this->renderLayout();
    }

    public function  indexAction(){
        if(!Mage::helper('customer')->isLoggedIn()){
          $this->_redirect('customer/account/');
        }
        $this->loadLayout();
        $this->renderLayout();
    }
    public function  saveAction()
    {
        if(!Mage::helper('customer')->isLoggedIn()){
            $this->_redirect('customer/account/');
        }
        $customerData = Mage::getSingleton('customer/session')->getCustomer();
        if ($postData = $this->getRequest()->getPost()) {
                if( !empty($_FILES['image']['name']) ){
                  try {
                    $uploader = new Varien_File_Uploader('image');
                    $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
                    $uploader->setAllowRenameFiles(true);
                    $uploader->setFilesDispersion(false);
                    $path = Mage::getBaseDir('media') . DS; // where we save images
                    $result = $uploader->save($path.'posts/image', $_FILES['image']['name']);
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
            }
           $post = Mage::getSingleton('imi_blog/posts');

                $post
                    ->setTitle($postData['title'])
                    ->setDescription($postData['content'])
                    ->setImage('/'.$_FILES['image']['name'])
                ->setCategoryId($postData['category'])
                ->setIsAdmin(0)
                ->setPostedById($customerData->getId())
                ->setUrlKey(md5($customerData->getId().rand(10000,100000000).$postData['title']))
                ->setStatus(1);
            try {
                $post->save();
                Mage::getSingleton("core/session")->addSuccess(Mage::helper('contacts')->__('Your inquiry was submitted and will be responded to as soon as possible. Thank you for contacting us.'));
                $this->_redirect('*/*/index');
                return;
            }
            catch (Mage_Core_Exception $e) {
              Mage::getSingleton('core/session')->addError($e->getMessage());
            }
            catch (Exception $e) {
                Mage::getSingleton('core/session')->addError($this->__('An error occurred while saving this baz.'));
            }
            $this->_redirect('*/*/');
          }
    }

}
