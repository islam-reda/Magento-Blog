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
 * Posts admin edit form
 *
 * @category    IMI
 * @package     IMI_Blog
 * @author      Ultimate Module Creator
 */
class IMI_Blog_Block_Adminhtml_Posts_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * constructor
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function __construct()
    {
        parent::__construct();
        $this->_blockGroup = 'imi_blog';
        $this->_controller = 'adminhtml_posts';
        $this->_updateButton(
            'save',
            'label',
            Mage::helper('imi_blog')->__('Save Posts')
        );
        $this->_updateButton(
            'delete',
            'label',
            Mage::helper('imi_blog')->__('Delete Posts')
        );
        $this->_addButton(
            'saveandcontinue',
            array(
                'label'   => Mage::helper('imi_blog')->__('Save And Continue Edit'),
                'onclick' => 'saveAndContinueEdit()',
                'class'   => 'save',
            ),
            -100
        );
        $this->_formScripts[] = "
            function saveAndContinueEdit() {
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    /**
     * get the edit form header
     *
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getHeaderText()
    {
        if (Mage::registry('current_posts') && Mage::registry('current_posts')->getId()) {
            return Mage::helper('imi_blog')->__(
                "Edit Posts '%s'",
                $this->escapeHtml(Mage::registry('current_posts')->getTitle())
            );
        } else {
            return Mage::helper('imi_blog')->__('Add Posts');
        }
    }
}
