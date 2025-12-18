<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Customer
 */

/**
 * Customer address config
 *
 * @package    Mage_Customer
 */
class Mage_Customer_Model_Address_Config extends Mage_Core_Model_Config_Base
{
    public const DEFAULT_ADDRESS_RENDERER  = 'customer/address_renderer_default';

    public const XML_PATH_ADDRESS_TEMPLATE = 'customer/address_templates/';

    public const DEFAULT_ADDRESS_FORMAT    = 'oneline';

    /**
     * Customer Address Templates per store
     *
     * @var array
     */
    protected $_types           = [];

    /**
     * Current store instance
     *
     * @var null|Mage_Core_Model_Store
     */
    protected $_store           = null;

    /**
     * Default types per store
     * Using for invalid code
     *
     * @var array
     */
    protected $_defaultTypes    = [];

    /**
     * @var array
     */
    private $_defaultType       = [];

    /**
     * @param null|bool|int|Mage_Core_Model_Store|string $store
     * @return $this
     * @throws Mage_Core_Model_Store_Exception
     */
    public function setStore($store)
    {
        $this->_store = Mage::app()->getStore($store);
        return $this;
    }

    /**
     * Retrieve store
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        if (is_null($this->_store)) {
            $this->_store = Mage::app()->getStore();
        }

        return $this->_store;
    }

    /**
     * Define node
     */
    public function __construct()
    {
        parent::__construct(Mage::getConfig()->getNode()->global->customer->address);
    }

    /**
     * Retrieve address formats
     *
     * @return Varien_Object[]
     */
    public function getFormats()
    {
        $store = $this->getStore();
        $storeId = $store->getId();
        if (!isset($this->_types[$storeId])) {
            $this->_types[$storeId] = [];
            foreach ($this->getNode('formats')->children() as $typeCode => $typeConfig) {
                $path = sprintf('%s%s', self::XML_PATH_ADDRESS_TEMPLATE, $typeCode);
                $type = new Varien_Object();
                $htmlEscape = strtolower((string) $typeConfig->htmlEscape);
                $htmlEscape = !(in_array($htmlEscape, ['false', '0', 'no'])
                    || !strlen($htmlEscape));
                $type->setCode($typeCode)
                    ->setTitle((string) $typeConfig->title)
                    ->setDefaultFormat(Mage::getStoreConfig($path, $store))
                    ->setHtmlEscape($htmlEscape);

                $renderer = (string) $typeConfig->renderer;
                if (!$renderer) {
                    $renderer = self::DEFAULT_ADDRESS_RENDERER;
                }

                $type->setRenderer(
                    Mage::helper('customer/address')->getRenderer($renderer)->setType($type),
                );

                $this->_types[$storeId][] = $type;
            }
        }

        return $this->_types[$storeId];
    }

    /**
     * Retrieve default address format
     *
     * @return Varien_Object
     */
    protected function _getDefaultFormat()
    {
        $store = $this->getStore();
        $storeId = $store->getId();
        if (!isset($this->_defaultType[$storeId])) {
            $this->_defaultType[$storeId] = new Varien_Object();
            $this->_defaultType[$storeId]->setCode('default')
                ->setDefaultFormat('{{depend prefix}}{{var prefix}} {{/depend}}{{var firstname}} {{depend middlename}}'
                        . '{{var middlename}} {{/depend}}{{var lastname}}{{depend suffix}} {{var suffix}}{{/depend}}, '
                        . '{{var street}}, {{var city}}, {{var region}} {{var postcode}}, {{var country}}');

            $this->_defaultType[$storeId]->setRenderer(
                Mage::helper('customer/address')
                    ->getRenderer(self::DEFAULT_ADDRESS_RENDERER)->setType($this->_defaultType[$storeId]),
            );
        }

        return $this->_defaultType[$storeId];
    }

    /**
     * Retrieve address format by code
     *
     * @param string $typeCode
     * @return Varien_Object
     */
    public function getFormatByCode($typeCode)
    {
        foreach ($this->getFormats() as $type) {
            if ($type->getCode() == $typeCode) {
                return $type;
            }
        }

        return $this->_getDefaultFormat();
    }
}
