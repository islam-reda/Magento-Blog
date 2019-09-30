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
 * Category model
 *
 * @category    IMI
 * @package     IMI_Blog
 * @author      Ultimate Module Creator
 */
class IMI_Blog_Model_Sourcemodel_Categories extends Mage_Eav_Model_Entity_Attribute_Source_Abstract{
    public function getAllOptions($withEmpty = true){
        $_options =  array();
        $categories = Mage::getModel('imi_blog/category')
          ->getCollection()
           ->addFieldToFilter('status', 1);
        foreach ($categories as $key => $category) {
          $sprator = '';
          for ($i=1; $i < $category->getLevel(); $i++) {
             $sprator .= '--';
          }
          $_options[] = array(
               'value' => $category->getId(),
               'label' => $sprator.' '.$category->getName()
           );
        }
        $options = $_options;
        if ($withEmpty) {
            array_unshift($options, array('value'=>'', 'label'=>'Please select an Option'));
        }
        return $options;
    }
    public function getOptionText($value)
    {
        $options = $this->getAllOptions(false);

        foreach ($options as $item) {
            if ($item['value'] == $value) {
                return $item['label'];
            }
        }
        return false;
    }
}
