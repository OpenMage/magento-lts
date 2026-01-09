<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Abstract API2 class for product website resource
 *
 * @package    Mage_Catalog
 */
abstract class Mage_Catalog_Model_Api2_Product_Website_Rest extends Mage_Catalog_Model_Api2_Product_Website
{
    /**
     * Product website retrieve is not available
     */
    protected function _retrieve()
    {
        $this->_critical(self::RESOURCE_METHOD_NOT_ALLOWED);
    }

    /**
     * Get product websites list
     *
     * @return array
     */
    protected function _retrieveCollection()
    {
        $return = [];
        foreach ($this->_loadProductById($this->getRequest()->getParam('product_id'))->getWebsiteIds() as $websiteId) {
            $return[] = ['website_id' => $websiteId];
        }

        return $return;
    }

    /**
     * Product website assign
     *
     * @return string
     */
    protected function _create(array $data)
    {
        $product = $this->_loadProductById($this->getRequest()->getParam('product_id'));

        /** @var Mage_Catalog_Model_Api2_Product_Website_Validator_Admin_Website $validator */
        $validator = Mage::getModel('catalog/api2_product_website_validator_admin_website');
        if (!$validator->isValidDataForWebsiteAssignmentToProduct($product, $data)) {
            foreach ($validator->getErrors() as $error) {
                $this->_error($error, Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
            }

            $this->_critical(self::RESOURCE_DATA_PRE_VALIDATION_ERROR);
        }

        $websiteIds = $product->getWebsiteIds();
        /** @var Mage_Core_Model_Website $website */
        $website = Mage::getModel('core/website')->load($data['website_id']);
        $websiteIds[] = $website->getId(); // Existence of a website is checked in the validator
        $product->setWebsiteIds($websiteIds);

        try {
            $product->save();

            /**
             * Do copying data to stores
             */
            if (isset($data['copy_to_stores'])) {
                foreach ($data['copy_to_stores'] as $storeData) {
                    Mage::getModel('catalog/product')
                        ->setStoreId($storeData['store_from'])
                        ->load($product->getId())
                        ->setStoreId($storeData['store_to'])
                        ->save();
                }
            }
        } catch (Mage_Core_Exception $e) {
            $this->_critical($e->getMessage(), Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_critical(self::RESOURCE_INTERNAL_ERROR);
        }

        return $this->_getLocation($website);
    }

    /**
     * Product website assign
     */
    protected function _multiCreate(array $data)
    {
        $product = $this->_loadProductById($this->getRequest()->getParam('product_id'));
        $websiteIds = $product->getWebsiteIds();
        foreach ($data as $singleData) {
            try {
                if (!is_array($singleData)) {
                    $this->_errorMessage(self::RESOURCE_DATA_INVALID, Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
                    $this->_critical(self::RESOURCE_DATA_PRE_VALIDATION_ERROR);
                }

                /** @var Mage_Catalog_Model_Api2_Product_Website_Validator_Admin_Website $validator */
                $validator = Mage::getModel('catalog/api2_product_website_validator_admin_website');
                if (!$validator->isValidDataForWebsiteAssignmentToProduct($product, $singleData)) {
                    foreach ($validator->getErrors() as $error) {
                        $this->_errorMessage($error, Mage_Api2_Model_Server::HTTP_BAD_REQUEST, [
                            'website_id' => $singleData['website_id'] ?? null,
                            'product_id' => $product->getId(),
                        ]);
                    }

                    $this->_critical(self::RESOURCE_DATA_PRE_VALIDATION_ERROR);
                }

                /** @var Mage_Core_Model_Website $website */
                $website = Mage::getModel('core/website')->load($singleData['website_id']);
                $websiteIds[] = $website->getId(); // Existence of a website is checked in the validator
                $product->setWebsiteIds($websiteIds);

                $product->save();

                /**
                 * Do copying data to stores
                 */
                if (isset($singleData['copy_to_stores'])) {
                    foreach ($singleData['copy_to_stores'] as $storeData) {
                        Mage::getModel('catalog/product')
                            ->setStoreId($storeData['store_from'])
                            ->load($product->getId())
                            ->setStoreId($storeData['store_to'])
                            ->save();
                    }
                }

                $this->_successMessage(
                    Mage_Api2_Model_Resource::RESOURCE_UPDATED_SUCCESSFUL,
                    Mage_Api2_Model_Server::HTTP_OK,
                    [
                        'website_id' => $website->getId(),
                        'product_id' => $product->getId(),
                    ],
                );
            } catch (Mage_Api2_Exception $e) {
                // pre-validation errors are already added
                if ($e->getMessage() != self::RESOURCE_DATA_PRE_VALIDATION_ERROR) {
                    $this->_errorMessage(
                        $e->getMessage(),
                        $e->getCode(),
                        [
                            'website_id' => $singleData['website_id'] ?? null,
                            'product_id' => $product->getId(),
                        ],
                    );
                }
            } catch (Exception) {
                $this->_errorMessage(
                    Mage_Api2_Model_Resource::RESOURCE_INTERNAL_ERROR,
                    Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR,
                    [
                        'website_id' => $singleData['website_id'] ?? null,
                        'product_id' => $product->getId(),
                    ],
                );
            }
        }
    }

    /**
     * Product websites update is not available
     */
    protected function _update(array $data)
    {
        $this->_critical(self::RESOURCE_METHOD_NOT_ALLOWED);
    }

    /**
     * Product website unassign
     */
    protected function _delete()
    {
        $product = $this->_loadProductById($this->getRequest()->getParam('product_id'));

        $website = $this->_loadWebsiteById($this->getRequest()->getParam('website_id'));

        /** @var Mage_Catalog_Model_Api2_Product_Website_Validator_Admin_Website $validator */
        $validator = Mage::getModel('catalog/api2_product_website_validator_admin_website');
        if (!$validator->isWebsiteAssignedToProduct($website, $product)) {
            foreach ($validator->getErrors() as $error) {
                $this->_error($error, Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
            }

            $this->_critical(self::RESOURCE_DATA_PRE_VALIDATION_ERROR);
        }

        $websiteIds = $product->getWebsiteIds();
        // Existence of a key is checked in the validator
        unset($websiteIds[array_search($website->getId(), $websiteIds)]);
        $product->setWebsiteIds($websiteIds);

        try {
            $product->save();
        } catch (Mage_Core_Exception $e) {
            $this->_critical($e->getMessage(), Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_critical(self::RESOURCE_INTERNAL_ERROR);
        }
    }

    /**
     * Get resource location
     *
     * @param  Mage_Core_Model_Website $website
     * @return string                  URL
     */
    protected function _getLocation($website)
    {
        /** @var Mage_Api2_Model_Route_ApiType $apiTypeRoute */
        $apiTypeRoute = Mage::getModel('api2/route_apiType');

        $chain = $apiTypeRoute->chain(
            new Zend_Controller_Router_Route($this->getConfig()->getRouteWithEntityTypeAction($this->getResourceType())),
        );
        $params = [
            'api_type' => $this->getRequest()->getApiType(),
            'product_id' => $this->getRequest()->getParam('product_id'),
            'website_id' => $website->getId(),
        ];
        $uri = $chain->assemble($params);

        return '/' . $uri;
    }
}
