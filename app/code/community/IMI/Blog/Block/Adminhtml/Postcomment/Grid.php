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
class IMI_Blog_Block_Adminhtml_Postcomment_Grid extends Mage_Adminhtml_Block_Widget_Grid
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
        $collection = Mage::getModel('imi_blog/postcomment')
            ->getCollection();

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
            'post_id',
            array(
                'header'    => Mage::helper('imi_blog')->__('Post Tilte'),
                'align'     => 'left',
                'index'     => 'post_id',
                'renderer'  => 'IMI_Blog_Block_Adminhtml_Postcomment_Renderer_Posts',
                'filter_condition_callback'=> array($this, '_posttitlefitler'),

            )
        );

        $this->addColumn(
            'comment',
            array(
                'header'    => Mage::helper('imi_blog')->__('Comment'),
                'align'     => 'left',
                'index'     => 'comment',
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
            'repond_name',
            array(
                'header' => Mage::helper('imi_blog')->__('Repond'),
                'index'  => 'repond_name',
                'type'=> 'text',
                'renderer'  => 'IMI_Blog_Block_Adminhtml_Postcomment_Renderer_Repondname',
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

        $this->addExportType('*/*/exportCsv', Mage::helper('imi_blog')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('imi_blog')->__('Excel'));
        $this->addExportType('*/*/exportXml', Mage::helper('imi_blog')->__('XML'));

        return parent::_prepareColumns();
    }

    /**
     * prepare mass action
     *
     * @access protected
     * @return IMI_Blog_Block_Adminhtml_Postcomment_Grid
     * @author Ultimate Module Creator
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('postcomment');
        $this->getMassactionBlock()->addItem(
            'delete',
            array(
                'label'=> Mage::helper('imi_blog')->__('Delete'),
                'url'  => $this->getUrl('*/*/massDeletelog'),
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
        //return $this->getUrl('*/*/edit', array('id' => $row->getId()));
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
     * @return IMI_Blog_Block_Adminhtml_Postcomment_Grid
     * @author Ultimate Module Creator
     */
    protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }


    //_posttitlefitler

    protected function _posttitlefitler($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }
        $title = $value;
       $posts = Mage::getModel('imi_blog/posts')
           ->getCollection()
           ->addFieldToFilter('title',array('like'=> '%'.$title.'%'));
       $postsids = array();
        foreach ($posts as $post){

            $postsids[] = $post->getId();
        }

        $this->getCollection()
            ->addFieldToFilter('post_id',array('in'=> $postsids));

        return $this;
    }
}
