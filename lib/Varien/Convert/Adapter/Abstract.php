<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Varien
 * @package     Varien_Convert
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Convert abstract adapter
 *
 * @category   Varien
 * @package    Varien_Convert
 * @author      Magento Core Team <core@magentocommerce.com>
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
