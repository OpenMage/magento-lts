<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_ImportExport
 */

/**
 * Import proxy product model
 *
 * @category   Mage
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
