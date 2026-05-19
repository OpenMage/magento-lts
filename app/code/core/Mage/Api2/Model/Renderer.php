<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Api2
 */

/**
 * Webservice API2 renderer factory model
 *
 * @package    Mage_Api2
 */
abstract class Mage_Api2_Model_Renderer
{
    /**
     * Get Renderer of given type
     *
     * @param  array|string                   $acceptTypes
     * @return false|Mage_Core_Model_Abstract
     * @throws Mage_Api2_Exception
     */
    public static function factory($acceptTypes)
    {
        /** @var Mage_Api2_Helper_Data $helper */
        $helper   = Mage::helper('api2');
        $adapters = $helper->getResponseRenderAdapters();

        if (!is_array($acceptTypes)) {
            $acceptTypes = [$acceptTypes];
        }

        $type = null;
        $adapterPath = null;
        foreach ($acceptTypes as $type) {
            foreach ($adapters as $item) {
                $itemType = $item->type;
                if (in_array($type, [$itemType, current(explode('/', $itemType)) . '/*', '*/*'])) {
                    $adapterPath = $item->model;
                    break 2;
                }
            }
        }

        //if server can't respond in any of accepted types it SHOULD send 406(not acceptable)
        if ($adapterPath === null) {
            throw new Mage_Api2_Exception(
                'Server can not understand Accept HTTP header media type.',
                Mage_Api2_Model_Server::HTTP_NOT_ACCEPTABLE,
            );
        }

        $adapter = Mage::getModel($adapterPath);
        if (!$adapter) {
            throw new Exception(sprintf('Response renderer adapter for content type "%s" not found.', $type));
        }

        return $adapter;
    }
}
