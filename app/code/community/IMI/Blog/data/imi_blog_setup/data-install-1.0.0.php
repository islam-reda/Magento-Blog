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
 /*
 * @category    IMI
 * @package     IMI_Blog
 * @author      Ultimate Module Creator
 */
Mage::getModel('imi_blog/category')
    ->load(1)
    ->setParentId(0)
    ->setPath(1)
    ->setLevel(0)
    ->setPosition(0)
    ->setChildrenCount(0)
    ->setName('ROOT')
    ->setInitialSetupFlag(true)
    ->save();
