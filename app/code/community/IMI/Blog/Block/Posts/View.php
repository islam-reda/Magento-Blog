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
 * Posts view block
 *
 * @category    IMI
 * @package     IMI_Blog
 * @author      Ultimate Module Creator
 */
class IMI_Blog_Block_Posts_View extends IMI_Blog_Block_Posts_List
{
    /**
     * get the current posts
     *
     * @access public
     * @return mixed (IMI_Blog_Model_Posts|null)
     * @author Ultimate Module Creator
     */
    public function getCurrentPosts()
    {
        return Mage::registry('current_posts');
    }
}
