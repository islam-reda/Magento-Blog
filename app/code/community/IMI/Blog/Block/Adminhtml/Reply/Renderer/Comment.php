<?php
/**
 * Created by PhpStorm.
 * User: it
 * Date: 18/05/16
 * Time: 4:49 PM
 */


class IMI_Blog_Block_Adminhtml_Reply_Renderer_Comment
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input
{

    public function render(Varien_Object $row)
    {
        $commentId = $row->getCommentId();
        $comment = Mage::getModel('imi_blog/postcomment')->load($commentId);
        return $comment->getComment();
    }
}
