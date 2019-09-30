<?php
/**
 * Created by PhpStorm.
 * User: it
 * Date: 18/05/16
 * Time: 4:49 PM
 */


class IMI_Blog_Block_Adminhtml_Posts_Renderer_Reply
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input
{

    public function render(Varien_Object $row)
    {
        $commentId  = $row->getId();
        $reply = Mage::getModel('imi_blog/reply')->load($commentId,'comment_id');
        if ($reply && $reply->getId()){
            return $this->__('Replied');
        }
        else{
            return $this->__('Not Replied');
        }
    }
}
