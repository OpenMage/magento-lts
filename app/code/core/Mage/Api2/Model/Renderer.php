<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Api2
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Webservice API2 renderer factory model
 *
 * @category   Mage
 * @package    Mage_Api2
 */
abstract class Mage_Api2_Model_Renderer
{
    /**
     * Get Renderer of given type
     *
     * @param array|string $acceptTypes
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
                if ($type == $itemType
                    || $type == current(explode('/', $itemType)) . '/*' || $type == '*/*'
                ) {
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
