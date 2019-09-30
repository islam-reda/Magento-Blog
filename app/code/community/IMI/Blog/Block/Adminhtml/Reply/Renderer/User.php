<?php
/**
 * Created by PhpStorm.
 * User: it
 * Date: 18/05/16
 * Time: 4:49 PM
 */


class IMI_Blog_Block_Adminhtml_Reply_Renderer_User
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input
{

    public function render(Varien_Object $row)
    {
        $UserId = $row->getUserId();
        $user = Mage::getModel('admin/user')->load($UserId);
        $link = Mage::helper("adminhtml")->getUrl("adminhtml/permissions_user/edit/",array("user_id"=>$UserId));
        return '<a href="'.$link.'">'.$user->getUsername().'</a>';
    }
}
