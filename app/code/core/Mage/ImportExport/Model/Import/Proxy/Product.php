<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_ImportExport
 */

/**
 * Import proxy product model
 *
 * @package    Mage_ImportExport
 */
class Mage_ImportExport_Model_Import_Proxy_Product extends Mage_Catalog_Model_Product
{
    /**
     * DO NOT Initialize resources.
     */
    protected function _construct() {}

    /**
     * Retrieve object id
     *
     * @return int
     */
    public function getId()
    {
        return $this->_getData('id');
    }
}
