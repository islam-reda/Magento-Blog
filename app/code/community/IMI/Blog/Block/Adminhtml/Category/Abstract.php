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
 * Category admin block abstract
 *
 * @category    IMI
 * @package     IMI_Blog
 * @author      Ultimate Module Creator
 */
class IMI_Blog_Block_Adminhtml_Category_Abstract extends Mage_Adminhtml_Block_Template
{
    /**
     * get current category
     *
     * @access public
     * @return IMI_Blog_Model_Entity
     * @author Ultimate Module Creator
     */
    public function getCategory()
    {
        return Mage::registry('category');
    }

    /**
     * get current category id
     *
     * @access public
     * @return int
     * @author Ultimate Module Creator
     */
    public function getCategoryId()
    {
        if ($this->getCategory()) {
            return $this->getCategory()->getId();
        }
        return null;
    }

    /**
     * get current category Name
     *
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getCategoryName()
    {
        return $this->getCategory()->getName();
    }

    /**
     * get current category path
     *
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getCategoryPath()
    {
        if ($this->getCategory()) {
            return $this->getCategory()->getPath();
        }
        return Mage::helper('imi_blog/category')->getRootCategoryId();
    }

    /**
     * check if there is a root category
     *
     * @access public
     * @return bool
     * @author Ultimate Module Creator
     */
    public function hasRootCategory()
    {
        $root = $this->getRoot();
        if ($root && $root->getId()) {
            return true;
        }
        return false;
    }

    /**
     * get the root
     *
     * @access public
     * @param IMI_Blog_Model_Category|null $parentNodeCategory
     * @param int $recursionLevel
     * @return Varien_Data_Tree_Node
     * @author Ultimate Module Creator
     */
    public function getRoot($parentNodeCategory = null, $recursionLevel = 3)
    {
        if (!is_null($parentNodeCategory) && $parentNodeCategory->getId()) {
            return $this->getNode($parentNodeCategory, $recursionLevel);
        }
        $root = Mage::registry('root');
        if (is_null($root)) {
            $rootId = Mage::helper('imi_blog/category')->getRootCategoryId();
            $tree = Mage::getResourceSingleton('imi_blog/category_tree')
                ->load(null, $recursionLevel);
            if ($this->getCategory()) {
                $tree->loadEnsuredNodes($this->getCategory(), $tree->getNodeById($rootId));
            }
            $tree->addCollectionData($this->getCategoryCollection());
            $root = $tree->getNodeById($rootId);
            if ($root && $rootId != Mage::helper('imi_blog/category')->getRootCategoryId()) {
                $root->setIsVisible(true);
            } elseif ($root && $root->getId() == Mage::helper('imi_blog/category')->getRootCategoryId()) {
                $root->setName(Mage::helper('imi_blog')->__('Root'));
            }
            Mage::register('root', $root);
        }
        return $root;
    }

    /**
     * Get and register categories root by specified categories IDs
     *
     * @accsess public
     * @param array $ids
     * @return Varien_Data_Tree_Node
     * @author Ultimate Module Creator
     */
    public function getRootByIds($ids)
    {
        $root = Mage::registry('root');
        if (null === $root) {
            $categoryTreeResource = Mage::getResourceSingleton('imi_blog/category_tree');
            $ids     = $categoryTreeResource->getExistingCategoryIdsBySpecifiedIds($ids);
            $tree   = $categoryTreeResource->loadByIds($ids);
            $rootId = Mage::helper('imi_blog/category')->getRootCategoryId();
            $root   = $tree->getNodeById($rootId);
            if ($root && $rootId != Mage::helper('imi_blog/category')->getRootCategoryId()) {
                $root->setIsVisible(true);
            } elseif ($root && $root->getId() == Mage::helper('imi_blog/category')->getRootCategoryId()) {
                $root->setName(Mage::helper('imi_blog')->__('Root'));
            }
            $tree->addCollectionData($this->getCategoryCollection());
            Mage::register('root', $root);
        }
        return $root;
    }

    /**
     * get specific node
     *
     * @access public
     * @param IMI_Blog_Model_Category $parentNodeCategory
     * @param $int $recursionLevel
     * @return Varien_Data_Tree_Node
     * @author Ultimate Module Creator
     */
    public function getNode($parentNodeCategory, $recursionLevel = 2)
    {
        $tree = Mage::getResourceModel('imi_blog/category_tree');
        $nodeId     = $parentNodeCategory->getId();
        $parentId   = $parentNodeCategory->getParentId();
        $node = $tree->loadNode($nodeId);
        $node->loadChildren($recursionLevel);
        if ($node && $nodeId != Mage::helper('imi_blog/category')->getRootCategoryId()) {
            $node->setIsVisible(true);
        } elseif ($node && $node->getId() == Mage::helper('imi_blog/category')->getRootCategoryId()) {
            $node->setName(Mage::helper('imi_blog')->__('Root'));
        }
        $tree->addCollectionData($this->getCategoryCollection());
        return $node;
    }

    /**
     * get url for saving data
     *
     * @access public
     * @param array $args
     * @return string
     * @author Ultimate Module Creator
     */
    public function getSaveUrl(array $args = array())
    {
        $params = array('_current'=>true);
        $params = array_merge($params, $args);
        return $this->getUrl('*/*/save', $params);
    }

    /**
     * get url for edit
     *
     * @access public
     * @param array $args
     * @return string
     * @author Ultimate Module Creator
     */
    public function getEditUrl()
    {
        return $this->getUrl(
            "*/blog_category/edit",
            array('_current' => true, '_query'=>false, 'id' => null, 'parent' => null)
        );
    }

    /**
     * Return root ids
     *
     * @access public
     * @return array
     * @author Ultimate Module Creator
     */
    public function getRootIds()
    {
        return array(Mage::helper('imi_blog/category')->getRootCategoryId());
    }
}
