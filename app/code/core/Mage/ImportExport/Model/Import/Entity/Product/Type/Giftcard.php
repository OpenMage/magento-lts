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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_ImportExport
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
 
/**
 * Import entity gift card product type model
 *
 * @category    Mage
 * @package     Mage_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_ImportExport_Model_Import_Entity_Product_Type_Giftcard
    extends Mage_ImportExport_Model_Import_Entity_Product_Type_Abstract
{
    /**
     * Error codes.
     */
    const ERROR_INVALID_GIFTCARD_AMOUNT_WEBSITE = 'invalidGiftcardAmountWebsite';
    const ERROR_INVALID_GIFTCARD_AMOUNT         = 'invalidGiftcardAmount';

    /**
     * Attributes' codes which will be allowed anyway, independently from its visibility propertie.
     *
     * @var array
     */
    protected $_forcedAttributesCodes = array(
        'allow_message', 'email_template', 'giftcard_type', 'is_redeemable', 'lifetime',
        'use_config_allow_message', 'use_config_email_template', 'use_config_is_redeemable', 'use_config_lifetime'
    );

    /**
     * Attributes with index (not label) value.
     *
     * @var array
     */
    protected $_indexValueAttributes = array('allow_open_amount', 'giftcard_type');

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates = array(
        self::ERROR_INVALID_GIFTCARD_AMOUNT_WEBSITE => 'Invalid value for GiftCard amount website',
        self::ERROR_INVALID_GIFTCARD_AMOUNT         => 'Invalid GiftCard amount'
    );

    /**
     * Column names that holds values with particular meaning.
     *
     * @var array
     */
    protected $_particularAttributes = array('_giftcard_amounts_website', '_giftcard_amounts_amount');

    /**
     * Validate particular attributes columns.
     *
     * @param array $rowData
     * @param int $rowNum
     * @return bool
     */
    protected function _isParticularAttributesValid(array $rowData, $rowNum)
    {
        if (!empty($rowData['_giftcard_amounts_website'])) {
            $websiteCode  = $rowData['_giftcard_amounts_website'];
            $websiteCodes = $this->_entityModel->getWebsiteCodes();

            if (Mage_ImportExport_Model_Import_Entity_Product::VALUE_ALL != $websiteCode
                    && !isset($websiteCodes[$websiteCode])) {
                $this->_entityModel->addRowError(self::ERROR_INVALID_GIFTCARD_AMOUNT_WEBSITE, $rowNum);
                return false;
            }
            if (!isset($rowData['_giftcard_amounts_amount']) || $rowData['_giftcard_amounts_amount'] < 0) {
                $this->_entityModel->addRowError(self::ERROR_INVALID_GIFTCARD_AMOUNT, $rowNum);
                return false;
            }
        }
        return true;
    }

    /**
     * Additional check for model availability. If method returns FALSE - model is not suitable for data processing.
     *
     * @return bool
     */
    public function isSuitable()
    {
        $moduleNode = Mage::getConfig()->getNode('modules/Enterprise_GiftCard/active');

        return $moduleNode && 'true' == (string) $moduleNode;
    }

    /**
     * Save product type specific data.
     *
     * @return Mage_ImportExport_Model_Import_Entity_Product_Type_Abstract
     */
    public function saveData()
    {
        $connection    = Mage::getSingleton('core/resource')->getConnection('write');
        $table         = Mage::getSingleton('core/resource')->getTableName('enterprise_giftcard/amount');
        $newSku        = $this->_entityModel->getNewSku();
        $entityTypeId  = $this->_entityModel->getEntityTypeId();
        $websiteCodes  = $this->_entityModel->getWebsiteCodes();
        $priceIsGlobal = Mage::helper('catalog')->isPriceGlobal();

        while ($bunch = $this->_entityModel->getNextBunch()) {
            $amountData = array(
                'product_id' => array(),
                'amounts'    => array()
            );
            foreach ($bunch as $rowNum => $rowData) {
                if (!$this->_entityModel->isRowAllowedToImport($rowData, $rowNum)
                    || empty($rowData['_giftcard_amounts_website'])
                ) {
                    continue;
                }
                $scope = $this->_entityModel->getRowScope($rowData);
                if (Mage_ImportExport_Model_Import_Entity_Product::SCOPE_DEFAULT == $scope) {
                    $productData = $newSku[$rowData[Mage_ImportExport_Model_Import_Entity_Product::COL_SKU]];
                } else {
                    $collAttrSet = Mage_ImportExport_Model_Import_Entity_Product::COL_ATTR_SET;
                    $rowData[$collAttrSet] = $productData['attr_set_code'];
                    $rowData[Mage_ImportExport_Model_Import_Entity_Product::COL_TYPE] = $productData['type_id'];
                }
                $productId = $productData['entity_id'];

                if ($this->_type != $rowData[Mage_ImportExport_Model_Import_Entity_Product::COL_TYPE]) {
                    continue;
                }
                $attributes = $this->_getProductAttributes($rowData);
                $amountData['product_id'][$productId] = true;
                
                if (Mage_ImportExport_Model_Import_Entity_Product::VALUE_ALL == $rowData['_giftcard_amounts_website']
                    || Mage::app()->isSingleStoreMode() || $priceIsGlobal) {
                    $websiteId = 0;
                } else {
                    $websiteId = $websiteCodes[$rowData['_giftcard_amounts_website']];
                }
                $amountData['amounts'][] = array(
                    'website_id'     => $websiteId,
                    'value'          => $rowData['_giftcard_amounts_amount'],
                    'entity_id'      => $productId,
                    'entity_type_id' => $entityTypeId,
                    'attribute_id'   => $attributes['giftcard_amounts']['id']
                );
            }
            // remove old data
            if ($amountData['product_id']) {
                $connection->delete(
                    $table, $connection->quoteInto('entity_id IN (?)', array_keys($amountData['product_id']))
                );
            }
            // save amounts
            if ($amountData['amounts']) {
                $connection->insertMultiple($table, $amountData['amounts']);
            }
        }
        return $this;
    }
}
