<?php
/**
 * Import proxy product model
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
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
