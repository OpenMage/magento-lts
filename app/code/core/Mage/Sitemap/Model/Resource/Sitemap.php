<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
 * @package    Mage_Sitemap
 */

/**
 * Sitemap resource model
 *
 * @category   Mage
 * @package    Mage_Sitemap
 */
class Mage_Sitemap_Model_Resource_Sitemap extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('sitemap/sitemap', 'sitemap_id');
    }
}
