<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Api2
 */

/**
 * Request content interpreter factory
 *
 * @package    Mage_Api2
 */
abstract class Mage_Api2_Model_Request_Interpreter
{
    /**
     * Request body interpreters factory
     *
     * @param string $type
     * @return false|Mage_Core_Model_Abstract
     * @throws Mage_Api2_Exception
     */
    public static function factory($type)
    {
        /** @var Mage_Api2_Helper_Data $helper */
        $helper = Mage::helper('api2/data');
        $adapters = $helper->getRequestInterpreterAdapters();

        if (empty($adapters) || !is_array($adapters)) {
            throw new Exception('Request interpreter adapters is not set.');
        }

        $adapterModel = null;
        foreach ($adapters as $item) {
            $itemType = $item->type;
            if ($itemType == $type) {
                $adapterModel = $item->model;
                break;
            }
        }

        if ($adapterModel === null) {
            throw new Mage_Api2_Exception(
                sprintf('Server can not understand Content-Type HTTP header media type "%s"', $type),
                Mage_Api2_Model_Server::HTTP_BAD_REQUEST,
            );
        }

        $adapter = Mage::getModel($adapterModel);
        if (!$adapter) {
            throw new Exception(sprintf('Request interpreter adapter "%s" not found.', $type));
        }

        return $adapter;
    }
}
