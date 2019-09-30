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
 * Posts admin grid block
 *
 * @category    IMI
 * @package     IMI_Blog
 * @author      Ultimate Module Creator
 */
class IMI_Blog_Block_Adminhtml_Customerposts_Grid extends Mage_Adminhtml_Block_Widget_Grid
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
        $this->setId('customerinquiresGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * prepare collection
     *
     * @access protected
     * @return IMI_Blog_Block_Adminhtml_Posts_Grid
     * @author Ultimate Module Creator
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('imi_blog/posts')
            ->getCollection()
            ->addFieldToFilter('is_admin',0);


        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * prepare grid collection
     *
     * @access protected
     * @return IMI_Blog_Block_Adminhtml_Posts_Grid
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
            'title',
            array(
                'header'    => Mage::helper('imi_blog')->__('Title'),
                'align'     => 'left',
                'index'     => 'title',
            )
        );

        $this->addColumn(
            'status',
            array(
                'header'  => Mage::helper('imi_blog')->__('Status'),
                'index'   => 'status',
                'type'    => 'options',
                'options' => array(
                    '1' => Mage::helper('imi_blog')->__('Enabled'),
                    '0' => Mage::helper('imi_blog')->__('Disabled'),
                )
            )
        );

        $this->addColumn(
            'customer_posted_by_id',
            array(
                'header' => Mage::helper('imi_blog')->__('Customer Posted by'),
                'index'  => 'posted_by_id',
                'type'=> 'text',
                'renderer'  => 'IMI_Blog_Block_Adminhtml_Posts_Renderer_Customerpostedby',
                'filter_condition_callback'=> array($this, '_customerpostedbyfilter'),
            )
        );
        if (!Mage::app()->isSingleStoreMode() && !$this->_isExport) {
            $this->addColumn(
                'store_id',
                array(
                    'header'     => Mage::helper('imi_blog')->__('Store Views'),
                    'index'      => 'store_id',
                    'type'       => 'store',
                    'store_all'  => true,
                    'store_view' => true,
                    'sortable'   => false,
                    'filter_condition_callback'=> array($this, '_filterStoreCondition'),
                )
            );
        }
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
                        'caption' => Mage::helper('imi_blog')->__('Edit'),
                        'url'     => array('base'=> '*/*/edit'),
                        'field'   => 'id'
                    )
                ),
                'filter'    => false,
                'is_system' => true,
                'sortable'  => false,
            )
        );

        $this->addExportType('*/*/exportCsv', Mage::helper('imi_blog')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('imi_blog')->__('Excel'));
        $this->addExportType('*/*/exportXml', Mage::helper('imi_blog')->__('XML'));
        return parent::_prepareColumns();
    }

    /**
     * prepare mass action
     *
     * @access protected
     * @return IMI_Blog_Block_Adminhtml_Posts_Grid
     * @author Ultimate Module Creator
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('posts');
        $this->getMassactionBlock()->addItem(
            'delete',
            array(
                'label'=> Mage::helper('imi_blog')->__('Delete'),
                'url'  => $this->getUrl('*/*/massDelete'),
                'confirm'  => Mage::helper('imi_blog')->__('Are you sure?')
            )
        );
        $this->getMassactionBlock()->addItem(
            'status',
            array(
                'label'      => Mage::helper('imi_blog')->__('Change status'),
                'url'        => $this->getUrl('*/*/massStatus', array('_current'=>true)),
                'additional' => array(
                    'status' => array(
                        'name'   => 'status',
                        'type'   => 'select',
                        'class'  => 'required-entry',
                        'label'  => Mage::helper('imi_blog')->__('Status'),
                        'values' => array(
                            '1' => Mage::helper('imi_blog')->__('Enabled'),
                            '0' => Mage::helper('imi_blog')->__('Disabled'),
                        )
                    )
                )
            )
        );
        $this->getMassactionBlock()->addItem(
            'is_admin',
            array(
                'label'      => Mage::helper('imi_blog')->__('Change is admin'),
                'url'        => $this->getUrl('*/*/massIsAdmin', array('_current'=>true)),
                'additional' => array(
                    'flag_is_admin' => array(
                        'name'   => 'flag_is_admin',
                        'type'   => 'select',
                        'class'  => 'required-entry',
                        'label'  => Mage::helper('imi_blog')->__('is admin'),
                        'values' => array(
                                '1' => Mage::helper('imi_blog')->__('Yes'),
                                '0' => Mage::helper('imi_blog')->__('No'),
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
     * @param IMI_Blog_Model_Posts
     * @return string
     * @author Ultimate Module Creator
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
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
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

    /**
     * after collection load
     *
     * @access protected
     * @return IMI_Blog_Block_Adminhtml_Posts_Grid
     * @author Ultimate Module Creator
     */
    protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }

    /**
     * filter store column
     *
     * @access protected
     * @param IMI_Blog_Model_Resource_Posts_Collection $collection
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     * @return IMI_Blog_Block_Adminhtml_Posts_Grid
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


    protected function _adminpostedbyfilter($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }
        $nameofAdminOrCustomer = $value;


        $admins = Mage::getModel('admin/user')
            ->getCollection()
//            ->addNameToSelect()
            ->addFieldToFilter('username',array('like'=> '%'.$nameofAdminOrCustomer.'%'));


        $adminids = array();

        foreach ($admins as $admin){
                $adminids[] = $admin->getId();
        }

        $this->getCollection()
            ->addFieldToFilter('is_admin',1)
            ->addFieldToFilter('posted_by_id',array('in'=> $adminids));


        return $this;
    }




    protected function _filterStoreCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }
        $collection->addStoreFilter($value);
        return $this;
    }
}
