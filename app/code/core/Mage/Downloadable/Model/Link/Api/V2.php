<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Downloadable
 */

/**
 * Downloadable links API model
 *
 * @package    Mage_Downloadable
 */
class Mage_Downloadable_Model_Link_Api_V2 extends Mage_Downloadable_Model_Link_Api
{
    /**
     * Clean the object, leave only property values
     *
     * @param object $var
     * @param-out array $var
     */
    protected function _prepareData(&$var)
    {
        if (is_object($var)) {
            $var = get_object_vars($var);
            foreach ($var as &$value) {
                $this->_prepareData($value);
            }
        }
    }

    /**
     * Add downloadable content to product
     *
     * @param  int|string $productId
     * @param  object     $resource
     * @param  string     $resourceType
     * @param  int|string $store
     * @param  string     $identifierType ('sku'|'id')
     * @return bool
     */
    public function add($productId, $resource, $resourceType, $store = null, $identifierType = null)
    {
        $this->_prepareData($resource);
        return parent::add($productId, $resource, $resourceType, $store, $identifierType);
    }
}
