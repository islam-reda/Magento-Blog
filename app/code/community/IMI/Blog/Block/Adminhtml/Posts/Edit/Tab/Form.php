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
 * Posts edit form tab
 *
 * @category    IMI
 * @package     IMI_Blog
 * @author      Ultimate Module Creator
 */
class IMI_Blog_Block_Adminhtml_Posts_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * prepare the form
     *
     * @access protected
     * @return IMI_Blog_Block_Adminhtml_Posts_Edit_Tab_Form
     * @author Ultimate Module Creator
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('posts_');
        $form->setFieldNameSuffix('posts');
        $this->setForm($form);
        $fieldset = $form->addFieldset(
            'posts_form',
            array('legend' => Mage::helper('imi_blog')->__('Posts'))
        );
        $fieldset->addType(
            'image',
            Mage::getConfig()->getBlockClassName('imi_blog/adminhtml_posts_helper_image')
        );
        $fieldset->addField(
            'title',
            'text',
            array(
                'label' => Mage::helper('imi_blog')->__('Title'),
                'name'  => 'title',
                'required'  => true,
                'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'description',
            'editor',
            array(
                'label' => Mage::helper('imi_blog')->__('Content'),
                'name'  => 'description',
                'required'  => true,
                'class' => 'required-entry',
                'config'    => Mage::getSingleton('cms/wysiwyg_config')->getConfig(),
                'wysiwyg'   => true,

           )
        );

        $fieldset->addField(
            'image',
            'image',
            array(
                'label' => Mage::helper('imi_blog')->__('Photo'),
                'name'  => 'image',

           )
        );
        // $fieldset->addField(
        //     'is_admin',
        //     'select',
        //     array(
        //         'label' => Mage::helper('imi_blog')->__('is admin'),
        //         'name'  => 'is_admin',
        //
        //     'values'=> array(
        //         array(
        //             'value' => 1,
        //             'label' => Mage::helper('imi_blog')->__('Yes'),
        //         ),
        //         array(
        //             'value' => 0,
        //             'label' => Mage::helper('imi_blog')->__('No'),
        //         ),
        //     ),
        //    )
        // );
        // $fieldset->addField(
        //     'posted_by_id',
        //     'text',
        //     array(
        //         'label' => Mage::helper('imi_blog')->__('Posted by'),
        //         'name'  => 'posted_by_id',
        //         'required'  => true,
        //         'class' => 'required-entry',
        //
        //    )
        // );

        $fieldset->addField(
            'category_id',
            'select',
            array(
                'label' => Mage::helper('imi_blog')->__('Category'),
                'name'  => 'category_id',
                'required'  => true,
                'class' => 'required-entry',
                'values' => Mage::getModel('imi_blog/sourcemodel_categories')->getAllOptions()
           )
        );


        $fieldset->addField(
            'url_key',
            'text',
            array(
                'label' => Mage::helper('imi_blog')->__('Url key'),
                'name'  => 'url_key',
                'note'  => Mage::helper('imi_blog')->__('Relative to Website Base URL')
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
        if (Mage::app()->isSingleStoreMode()) {
            $fieldset->addField(
                'store_id',
                'hidden',
                array(
                    'name'      => 'stores[]',
                    'value'     => Mage::app()->getStore(true)->getId()
                )
            );
            Mage::registry('current_posts')->setStoreId(Mage::app()->getStore(true)->getId());
        }
        $formValues = Mage::registry('current_posts')->getDefaultValues();
        if (!is_array($formValues)) {
            $formValues = array();
        }
        if (Mage::getSingleton('adminhtml/session')->getPostsData()) {
            $formValues = array_merge($formValues, Mage::getSingleton('adminhtml/session')->getPostsData());
            Mage::getSingleton('adminhtml/session')->setPostsData(null);
        } elseif (Mage::registry('current_posts')) {
            $formValues = array_merge($formValues, Mage::registry('current_posts')->getData());
        }
        $form->setValues($formValues);
        return parent::_prepareForm();
    }
}
