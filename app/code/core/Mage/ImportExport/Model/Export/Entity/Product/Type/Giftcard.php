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
 * Export entity product type gift card model
 *
 * @category    Mage
 * @package     Mage_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_ImportExport_Model_Export_Entity_Product_Type_Giftcard
    extends Mage_ImportExport_Model_Export_Entity_Product_Type_Abstract
{
    /**
     * Overriden attributes parameters.
     *
     * @var array
     */
    protected $_attributeOverrides = array(
        'allow_message'             => array('source_model' => 'eav/entity_attribute_source_boolean'),
        'is_redeemable'             => array('source_model' => 'eav/entity_attribute_source_boolean'),
        'use_config_allow_message'  => array('source_model' => 'eav/entity_attribute_source_boolean'),
        'use_config_email_template' => array('source_model' => 'eav/entity_attribute_source_boolean'),
        'use_config_is_redeemable'  => array('source_model' => 'eav/entity_attribute_source_boolean'),
        'use_config_lifetime'       => array('source_model' => 'eav/entity_attribute_source_boolean'),
        'email_template'            => array('options_method' => 'getEmailTemplates')
    );

    /**
     * Array of attributes codes which are disabled for export.
     *
     * @var array
     */
    protected $_disabledAttrs = array('giftcard_amounts');

    /**
     * Attributes with index (not label) value.
     *
     * @var array
     */
    protected $_indexValueAttributes = array('allow_open_amount', 'giftcard_type');

    /**
     * Return email template select options.
     *
     * @return array
     */
    public function getEmailTemplates()
    {
        return Mage::getModel(
            Mage::getConfig()->getBlockClassName('enterprise_giftcard/adminhtml_catalog_product_edit_tab_giftcard')
        )->getEmailTemplates();
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
}
