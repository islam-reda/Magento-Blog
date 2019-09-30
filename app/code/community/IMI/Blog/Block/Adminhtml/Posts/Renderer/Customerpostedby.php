<?php
/**
 * Created by PhpStorm.
 * User: it
 * Date: 18/05/16
 * Time: 4:49 PM
 */


class IMI_Blog_Block_Adminhtml_Posts_Renderer_Customerpostedby
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input
{

    public function render(Varien_Object $row)
    {
        $isAdmin  = $row->getIsAdmin();
        $postedById = $row->getPostedById();
        if (!$isAdmin){
            $customer = Mage::getModel('customer/customer ')->load($postedById);
            return $customer->getName() ;
        }
        else{
           // $admin = Mage::getModel('admin/user ')->load($postedById);
            return "-";
        }


    }
}