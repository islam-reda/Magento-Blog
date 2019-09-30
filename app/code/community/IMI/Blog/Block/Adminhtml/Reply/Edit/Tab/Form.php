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
 * Reply edit form tab
 *
 * @category    IMI
 * @package     IMI_Blog
 * @author      Ultimate Module Creator
 */
class IMI_Blog_Block_Adminhtml_Reply_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * prepare the form
     *
     * @access protected
     * @return IMI_Blog_Block_Adminhtml_Reply_Edit_Tab_Form
     * @author Ultimate Module Creator
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('reply_');
        $form->setFieldNameSuffix('reply');
        $this->setForm($form);
        $fieldset = $form->addFieldset(
            'reply_form',
            array('legend' => Mage::helper('imi_blog')->__('Reply'))
        );
        $comment_id = $this->getRequest()->getParam('comment_id');
        if($comment_id){
            $fieldset->addField(
                'comment_id',
                'hidden',
                array(
                    'label' => Mage::helper('imi_blog')->__('Comment'),
                    'name'  => 'comment_id',
                    'required'  => true,
                    'class' => 'required-entry',

               )
            );
            $fieldset->addField(
                'reply',
                'text',
                array(
                    'label' => Mage::helper('imi_blog')->__('Reply'),
                    'name'  => 'reply',
                    'required'  => true,
                    'class' => 'required-entry',

               )
            );
          }

        $formValues = Mage::registry('current_reply')->getDefaultValues();
        if (!is_array($formValues)) {
            $formValues = array();
        }
        if (Mage::getSingleton('adminhtml/session')->getReplyData()) {
            $formValues = array_merge($formValues, Mage::getSingleton('adminhtml/session')->getReplyData());
            Mage::getSingleton('adminhtml/session')->setReplyData(null);
        } elseif (Mage::registry('current_reply')) {
            $formValues = array_merge($formValues, Mage::registry('current_reply')->getData());
        }
        if($comment_id){
          $formValues['comment_id'] = $comment_id;
        }
        $form->setValues($formValues);
        return parent::_prepareForm();
    }
}
