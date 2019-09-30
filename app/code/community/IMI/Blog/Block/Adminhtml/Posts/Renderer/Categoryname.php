<?php
/**
 * Created by PhpStorm.
 * User: it
 * Date: 18/05/16
 * Time: 4:49 PM
 */
class IMI_Blog_Block_Adminhtml_Posts_Renderer_Categoryname
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input
{
    public function render(Varien_Object $row)
    {


        $categoryId = $row->getCategoryId();

        $categoryRow = Mage::getModel('imi_blog/category ')->load($categoryId);


        //Zend_Debug::dump($categoryRow);
        // imi_blog/category ->load();

        return $categoryRow->getName();
    }

}