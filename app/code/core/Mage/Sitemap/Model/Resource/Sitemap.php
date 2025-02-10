<?php
/**
 * Sitemap resource model
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Sitemap
 */
class Mage_Sitemap_Model_Resource_Sitemap extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('sitemap/sitemap', 'sitemap_id');
    }
}
