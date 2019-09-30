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
 * Posts front contrller
 *
 * @category    IMI
 * @package     IMI_Blog
 * @author      Ultimate Module Creator
 */
class IMI_Blog_PostsController extends Mage_Core_Controller_Front_Action
{

    /**
      * default action
      *
      * @access public
      * @return void
      * @author Ultimate Module Creator
      */
    public function  getpostsbycategoryAction(){
        $category_id = $this->getRequest()->getParam('category_id');
        if($category_id){
            $html = $this->getLayout()->createBlock('imi_blog/posts_list')
              ->setTemplate('imi_blog/posts/list.phtml')->toHtml();
            $result = array(
              'success'=> 1,
              'html'=> $html,
            );
        }
      return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    public function viewcommentAction(){
      if(Mage::getSingleton('customer/session')->isLoggedIn())
      {
        $params = Mage::app()->getRequest()->getParams();
        $isSpam = $this->isSpam($params);
        if($isSpam){
          $result = array(
            'success'=> 0,
          );
          return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
        if($params['post_id'] && $params['comment']){
          $customer = Mage::getSingleton('customer/session')->getCustomer();
          $data = array(
            'post_id' => $params['post_id'],
            'repond_id'=> $customer->getId(),
            'repond_name'=> $customer->getName(),
            'comment'=> $params['comment'],
            'is_admin'=> 0,
            'status'=> !Mage::getStoreConfig('imi_blog/posts/approve'),
            'success'=> 1,
          );

          try {
            Mage::getModel('imi_blog/postcomment')->addData($data)->save();

            $html = $this->getLayout()->createBlock('imi_blog/posts_commentsview')
              ->setData('posts', Mage::getModel('imi_blog/posts')->load($params['post_id']))
              ->setTemplate('imi_blog/posts/comments.phtml')->toHtml();
            $result = array(
              'success'=> 1,
              'html'=> $html,
            );
          } catch(Exception $exception) {
            $result = array(
              'success'=> 0,
            );
          }
        }else{
          $result = array(
            'success'=> 0,
          );
        }
      }
      else
      {
        $result = array(
          'success'=> 2,
        );
      }

      return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }
    public function isSpam($params){
      $isSpam = false;
      $allowcaptcha = Mage::getStoreConfig('imi_blog/posts/allowcaptcha');
      if($allowcaptcha){
        $secretKey = Mage::getStoreConfig('imi_blog/posts/secret_key');
        if($params['g-recaptcha-response'] && $secretKey){
            $secret = Mage::getStoreConfig('imi_blog/posts/secret_key');
            $ip = $_SERVER['REMOTE_ADDR'];
            $captcha = $params['g-recaptcha-response'];
            $rsp  = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secret&response=$captcha&remoteip$ip");
            $arr = json_decode($rsp,TRUE);
            if(!$arr['success']){
                $isSpam = true;
            }
        }else{
            $isSpam = true;
        }
      }
      return $isSpam;
    }
    public function addcommentAction(){
      if(Mage::getSingleton('customer/session')->isLoggedIn())
      {
        $params = Mage::app()->getRequest()->getParams();
        $isSpam = $this->isSpam($params);
        if($isSpam){
          $result = array(
            'success'=> 0,
          );
          return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
        if($params['post_id'] && $params['comment']){
          $customer = Mage::getSingleton('customer/session')->getCustomer();
          $data = array(
            'post_id' => $params['post_id'],
            'repond_id'=> $customer->getId(),
            'repond_name'=> $customer->getName(),
            'comment'=> $params['comment'],
            'is_admin'=> 0,
            'status'=> !Mage::getStoreConfig('imi_blog/posts/approve'),
            'success'=> 1,
          );

          try {
            Mage::getModel('imi_blog/postcomment')->addData($data)->save();
            $html = $this->getLayout()->createBlock('imi_blog/posts_comments')
              ->setData('posts', Mage::getModel('imi_blog/posts')->load($params['post_id']))
              ->setTemplate('imi_blog/posts/comments.phtml')->toHtml();
            $result = array(
              'success'=> 1,
              'html'=> $html,
            );
          } catch(Exception $exception) {
            $result = array(
              'success'=> 0,
            );
          }
        }else{
          $result = array(
            'success'=> 0,
          );
        }
      }
      else
      {
        $result = array(
          'success'=> 2,
        );
      }

      return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }
    public function indexAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('checkout/session');
        if (Mage::helper('imi_blog/posts')->getUseBreadcrumbs()) {
            if ($breadcrumbBlock = $this->getLayout()->getBlock('breadcrumbs')) {
                $breadcrumbBlock->addCrumb(
                    'home',
                    array(
                        'label' => Mage::helper('imi_blog')->__('Home'),
                        'link'  => Mage::getUrl(),
                    )
                );
                $breadcrumbBlock->addCrumb(
                    'postss',
                    array(
                        'label' => Mage::helper('imi_blog')->__('Posts'),
                        'link'  => '',
                    )
                );
            }
        }
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->addLinkRel('canonical', Mage::helper('imi_blog/posts')->getPostssUrl());
        }
        if ($headBlock) {
            $headBlock->setTitle(Mage::getStoreConfig('imi_blog/posts/meta_title'));
            $headBlock->setKeywords(Mage::getStoreConfig('imi_blog/posts/meta_keywords'));
            $headBlock->setDescription(Mage::getStoreConfig('imi_blog/posts/meta_description'));
        }
        $this->renderLayout();
    }

    /**
     * init Posts
     *
     * @access protected
     * @return IMI_Blog_Model_Posts
     * @author Ultimate Module Creator
     */
    protected function _initPosts()
    {
        $postsId   = $this->getRequest()->getParam('id', 0);
        $posts     = Mage::getModel('imi_blog/posts')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($postsId);
        if (!$posts->getId()) {
            return false;
        } elseif (!$posts->getStatus()) {
            return false;
        }
        return $posts;
    }

    /**
     * view posts action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function viewAction()
    {
        $posts = $this->_initPosts();
        if (!$posts) {
            $this->_forward('no-route');
            return;
        }
        Mage::register('current_posts', $posts);
        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('checkout/session');
        if ($root = $this->getLayout()->getBlock('root')) {
            $root->addBodyClass('blog-posts blog-posts' . $posts->getId());
        }
        if (Mage::helper('imi_blog/posts')->getUseBreadcrumbs()) {
            if ($breadcrumbBlock = $this->getLayout()->getBlock('breadcrumbs')) {
                $breadcrumbBlock->addCrumb(
                    'home',
                    array(
                        'label'    => Mage::helper('imi_blog')->__('Home'),
                        'link'     => Mage::getUrl(),
                    )
                );
                $breadcrumbBlock->addCrumb(
                    'postss',
                    array(
                        'label' => Mage::helper('imi_blog')->__('Posts'),
                        'link'  => Mage::helper('imi_blog/posts')->getPostssUrl(),
                    )
                );
                $breadcrumbBlock->addCrumb(
                    'posts',
                    array(
                        'label' => $posts->getTitle(),
                        'link'  => '',
                    )
                );
            }
        }
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->addLinkRel('canonical', $posts->getPostsUrl());
        }
        if ($headBlock) {
            if ($posts->getMetaTitle()) {
                $headBlock->setTitle($posts->getMetaTitle());
            } else {
                $headBlock->setTitle($posts->getTitle());
            }
            $headBlock->setKeywords($posts->getMetaKeywords());
            $headBlock->setDescription($posts->getMetaDescription());
        }
        $this->renderLayout();
    }
}
