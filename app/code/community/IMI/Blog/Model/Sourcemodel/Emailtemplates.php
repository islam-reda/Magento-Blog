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
class IMI_Blog_Model_Sourcemodel_Emailtemplates extends Mage_Eav_Model_Entity_Attribute_Source_Abstract{
    public function getAllOptions($withEmpty = true){
        $_options =  array();
        $emails = Mage::getResourceModel('core/email_template_collection')
          ->load();
        foreach ($emails as $key => $email) {
          $_options[] = array(
               'value' => $email->getId(),
               'label' => $email->getName()
           );
        }
        $options = $_options;
        if ($withEmpty) {
            array_unshift($options, array('value'=>'', 'label'=>'Please select an Option'));
        }
        return $options;
    }
    public function toOptionArray()
    {
        $result = array();
        $collection = Mage::getResourceModel('core/email_template_collection')
            ->load();
        $options = $collection->toOptionArray();
        $defOptions = Mage_Core_Model_Email_Template::getDefaultTemplatesAsOptionsArray();
        foreach ($defOptions as $v) {
            $options[] = $v;
        }
        foreach ($options as $v) {
            $result[$v['value']] = $v['label'];
        }
        // sort by names alphabetically
        asort($result);
        if (!$asHash) {
            $options = array();
            $options[] = array('value' => '', 'label' => 'Choose Email Template');
            foreach ($result as $k => $v) {
                if ($k == '')
                    continue;
                $options[] = array('value' => $k, 'label' => $v);
            }

            $result = $options;
        }
        return $result;
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
