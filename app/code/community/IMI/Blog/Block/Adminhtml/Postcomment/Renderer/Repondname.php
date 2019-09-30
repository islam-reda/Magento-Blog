<?php
/**
 * Created by PhpStorm.
 * User: it
 * Date: 18/05/16
 * Time: 4:49 PM
 */


class IMI_Blog_Block_Adminhtml_Postcomment_Renderer_Repondname
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input
{

    public function render(Varien_Object $row)
    {

        $repondId = $row->getRepondId();
        $repondName = $row->getRepondName();
        if($row->getIsAdmin() == 1){
          $link = Mage::helper("adminhtml")->getUrl("adminhtml/permissions_user/edit/",array("user_id"=>$repondId));
        }else{
          $link = Mage::helper("adminhtml")->getUrl("adminhtml/customer/edit/",array("id"=>$repondId));
        }
        return '<a href="'.$link.'">'.$repondName.'</a>';
    }
}
