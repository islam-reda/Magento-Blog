<?php
/**
 * Created by PhpStorm.
 * User: it
 * Date: 18/05/16
 * Time: 4:49 PM
 */


class IMI_Blog_Block_Adminhtml_Postcomment_Renderer_Posts
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input
{

    public function render(Varien_Object $row)
    {

        $postId = $row->getPostId();
        $post = Mage::getModel('imi_blog/posts')->load($postId);
        if($post->getIsAdmin() == 1){
          $link = Mage::helper("adminhtml")->getUrl("adminhtml/blog_posts/edit/",array("id"=>$post->getId()));
        }else{
          $link = Mage::helper("adminhtml")->getUrl("adminhtml/blog_customerposts/edit/",array("id"=>$post->getId()));
        }
        return '<a href="'.$link.'">'.$post->getTitle().'</a>';
    }
}
