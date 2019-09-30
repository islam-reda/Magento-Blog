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
 * Posts image field renderer helper
 *
 * @category    IMI
 * @package     IMI_Blog
 * @author      Ultimate Module Creator
 */
 class IMI_Blog_Block_Adminhtml_Customerposts_Helper_Label extends Varien_Data_Form_Element_Text
{
    /**
     * Get escaped value
     *
     * format value to 2 decimal places
     */
    public function getElementHtml($index=null)
    {
        return $this->getData('value');
    }
}
