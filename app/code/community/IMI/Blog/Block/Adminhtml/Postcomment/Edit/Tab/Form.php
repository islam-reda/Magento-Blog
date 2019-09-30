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
 * Comment edit form tab
 *
 * @category    IMI
 * @package     IMI_Blog
 * @author      Ultimate Module Creator
 */
class IMI_Blog_Block_Adminhtml_Postcomment_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * prepare the form
     *
     * @access protected
     * @return IMI_Blog_Block_Adminhtml_Postcomment_Edit_Tab_Form
     * @author Ultimate Module Creator
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('postcomment_');
        $form->setFieldNameSuffix('postcomment');
        $this->setForm($form);
        $fieldset = $form->addFieldset(
            'postcomment_form',
            array('legend' => Mage::helper('imi_blog')->__('Comment'))
        );

        $fieldset->addField(
            'post_id',
            'text',
            array(
                'label' => Mage::helper('imi_blog')->__('Post Id'),
                'name'  => 'post_id',
                'required'  => true,
                'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'repond_id',
            'text',
            array(
                'label' => Mage::helper('imi_blog')->__('Repond'),
                'name'  => 'repond_id',
                'required'  => true,
                'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'repond_name',
            'text',
            array(
                'label' => Mage::helper('imi_blog')->__('Repond'),
                'name'  => 'repond_name',
                'required'  => true,
                'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'comment',
            'textarea',
            array(
                'label' => Mage::helper('imi_blog')->__('Comment'),
                'name'  => 'comment',
                'required'  => true,
                'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'is_admin',
            'select',
            array(
                'label' => Mage::helper('imi_blog')->__('Is admin'),
                'name'  => 'is_admin',

            'values'=> array(
                array(
                    'value' => 1,
                    'label' => Mage::helper('imi_blog')->__('Yes'),
                ),
                array(
                    'value' => 0,
                    'label' => Mage::helper('imi_blog')->__('No'),
                ),
            ),
           )
        );
        $fieldset->addField(
            'status',
            'select',
            array(
                'label'  => Mage::helper('imi_blog')->__('Status'),
                'name'   => 'status',
                'values' => array(
                    array(
                        'value' => 1,
                        'label' => Mage::helper('imi_blog')->__('Enabled'),
                    ),
                    array(
                        'value' => 0,
                        'label' => Mage::helper('imi_blog')->__('Disabled'),
                    ),
                ),
            )
        );
        $formValues = Mage::registry('current_postcomment')->getDefaultValues();
        if (!is_array($formValues)) {
            $formValues = array();
        }
        if (Mage::getSingleton('adminhtml/session')->getPostcommentData()) {
            $formValues = array_merge($formValues, Mage::getSingleton('adminhtml/session')->getPostcommentData());
            Mage::getSingleton('adminhtml/session')->setPostcommentData(null);
        } elseif (Mage::registry('current_postcomment')) {
            $formValues = array_merge($formValues, Mage::registry('current_postcomment')->getData());
        }
        $form->setValues($formValues);
        return parent::_prepareForm();
    }
}
