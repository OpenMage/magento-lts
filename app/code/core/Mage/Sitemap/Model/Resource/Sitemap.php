<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sitemap
 */

/**
 * Sitemap resource model
 *
 * @package    Mage_Sitemap
 */
class Mage_Sitemap_Model_Resource_Sitemap extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('sitemap/sitemap', 'sitemap_id');
    }
}
