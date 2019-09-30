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
 * Comment admin grid block
 *
 * @category    IMI
 * @package     IMI_Blog
 * @author      Ultimate Module Creator
 */
class IMI_Blog_Block_Adminhtml_Posts_Edit_Tab_Commentsgrid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * constructor
     *
     * @access public
     * @author Ultimate Module Creator
     */

    public function __construct()
    {
        parent::__construct();
        $this->setId('postcommentGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * prepare collection
     *
     * @access protected
     * @return IMI_Blog_Block_Adminhtml_Postcomment_Grid
     * @author Ultimate Module Creator
     */

    protected function _prepareCollection()
    {

        $id = $this->getRequest()->getParam('id');

        $collection = Mage::getModel('imi_blog/postcomment')
            ->getCollection()
            ->addFieldToFilter('post_id', $id);
        foreach ($collection as $key => $comment) {
            $reply = Mage::getModel('imi_blog/reply')->load($comment->getId(),'comment_id');
            if($reply && $reply->getId()){
              $comment->setReply(1);
            }else{
              $comment->setReply(0);
            }
        }

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * prepare grid collection
     *
     * @access protected
     * @return IMI_Blog_Block_Adminhtml_Postcomment_Grid
     * @author Ultimate Module Creator
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            array(
                'header' => Mage::helper('imi_blog')->__('Id'),
                'index'  => 'entity_id',
                'type'   => 'number'
            )
        );

        $this->addColumn(
            'repond_name',
            array(
                'header' => Mage::helper('imi_blog')->__('Customer Name'),
                'index'  => 'repond_name',
                'type'=> 'text',
                'renderer'  => 'IMI_Blog_Block_Adminhtml_Postcomment_Renderer_Repondname',
            )
        );

        $this->addColumn(
            'comment',
            array(
                'header' => Mage::helper('imi_blog')->__('Comment'),
                'index'  => 'comment',   // attribute code
                'type'=> 'text',
            )
        );
        $this->addColumn(
            'reply',
            array(
                'header' => Mage::helper('imi_blog')->__('is Replied'),
                'index'  => 'reply',   // attribute code
                'type'    => 'options',
                'options' => array(
                    '1' => Mage::helper('imi_blog')->__('Replied'),
                    '0' => Mage::helper('imi_blog')->__('Not Replied'),
                ),
                'filter'    => false,
                'sortable'  => false
            )
        );

        $this->addColumn(
            'status',
            array(
                'header'  => Mage::helper('imi_blog')->__('Status'),
                'index'   => 'status',
                'type'    => 'options',
                'options' => array(
                    '1' => Mage::helper('imi_blog')->__('Approved'),
                    '0' => Mage::helper('imi_blog')->__('Pending'),
                ),
            )
        );

        $this->addColumn(
            'created_at',
            array(
                'header' => Mage::helper('imi_blog')->__('Created at'),
                'index'  => 'created_at',
                'width'  => '120px',
                'type'   => 'datetime',
            )
        );

        $this->addColumn(
            'updated_at',
            array(
                'header'    => Mage::helper('imi_blog')->__('Updated at'),
                'index'     => 'updated_at',
                'width'     => '120px',
                'type'      => 'datetime',
            )
        );

        $this->addColumn(
            'action',
            array(
                'header'  =>  Mage::helper('imi_blog')->__('Action'),
                'width'   => '100',
                'type'    => 'action',
                'getter'  => 'getId',
                'actions' => array(
                    array(
                        'caption' => Mage::helper('imi_blog')->__('Reply'),
                        'url'     => array('base'=> '*/blog_reply/new'),
                        'field'   => 'comment_id'
                    ),
                    array(
                        'caption' => Mage::helper('imi_blog')->__('View Reply'),
                        'url'     => array('base'=> '*/blog_reply/index'),
                        'field'   => 'comment_id'
                    )
                ),
                'filter'    => false,
                'is_system' => true,
                'sortable'  => false,
            )
        );
        $this->addExportType('*/*/exportCsvComments', Mage::helper('imi_blog')->__('CSV'));
        $this->addExportType('*/*/exportExcelComments', Mage::helper('imi_blog')->__('Excel'));
        $this->addExportType('*/*/exportXmlComments', Mage::helper('imi_blog')->__('XML'));
        return parent::_prepareColumns();
    }

    /**
     * prepare mass action
     *
     * @access protected
     * @return IMI_Blog_Block_Adminhtml_Postcomment_Grid
     * @author Ultimate Module Creator
     */

     protected function _customerpostedbyfilter($collection, $column)
     {
         if (!$value = $column->getFilter()->getValue()) {
             return $this;
         }
         $nameofAdminOrCustomer = $value;


         $customers = Mage::getResourceModel('customer/customer_collection')
            ->addNameToSelect()
             ->addFieldToFilter('name',array('like'=> '%'.$nameofAdminOrCustomer.'%'));

         $customersids = array();

         foreach ($customers as $customer){
             $customersids[] = $customer->getId();
         }

         $this->getCollection()
             ->addFieldToFilter('is_admin',0)
             ->addFieldToFilter('posted_by_id',array('in'=> $customersids));


         return $this;
     }
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('postcomment');
        $this->getMassactionBlock()->addItem(
            'delete',
            array(
                'label'=> Mage::helper('imi_blog')->__('Delete'),
                'url'  => $this->getUrl('*/blog_postcomment/massDelete'),
                'confirm'  => Mage::helper('imi_blog')->__('Are you sure?')
            )
        );
        $this->getMassactionBlock()->addItem(
            'status',
            array(
                'label'      => Mage::helper('imi_blog')->__('Change status'),
                'url'        => $this->getUrl('*/blog_postcomment/massStatusPostComments', array('_current'=>true)),
                'additional' => array(
                    'status' => array(
                        'name'   => 'status',
                        'type'   => 'select',
                        'class'  => 'required-entry',
                        'label'  => Mage::helper('imi_blog')->__('Status'),
                        'values' => array(
                            '1' => Mage::helper('imi_blog')->__('Approved'),
                            '0' => Mage::helper('imi_blog')->__('Pending'),
                        )
                    )
                )
            )
        );
        return $this;
    }

    /**
     * get the row url
     *
     * @access public
     * @param IMI_Blog_Model_Postcomment
     * @return string
     * @author Ultimate Module Creator
     */
    public function getRowUrl($row)
    {
       // return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    /**
     * get the grid url
     *
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/commentgrid', array('_current'=>true));
    }

    /**
     * after collection load
     *
     * @access protected
     * @return IMI_Blog_Block_Adminhtml_Postcomment_Grid
     * @author Ultimate Module Creator
     */
    protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }
}
