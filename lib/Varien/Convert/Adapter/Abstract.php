<?php
/**
 * Convert abstract adapter
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Varien_Convert
 */
/**
 * @package    Varien_Convert
 */


abstract class Varien_Convert_Adapter_Abstract extends Varien_Convert_Container_Abstract implements Varien_Convert_Adapter_Interface
{
    /**
     * Adapter resource instance
     *
     * @var object
     */
    protected $_resource;

    /**
     * Retrieve resource generic method
     *
     * @return object
     */
    public function getResource()
    {
        return $this->_resource;
    }

    /**
     * Set resource for the adapter
     *
     * @param object $resource
     * @return Varien_Convert_Adapter_Abstract
     */
    public function setResource($resource)
    {
        $this->_resource = $resource;
        return $this;
    }
}
