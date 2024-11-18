<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Wishlist
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Wishlist block customer items
 *
 * @category   Mage
 * @package    Mage_Wishlist
 *
 * @method Mage_Wishlist_Model_Item getItem()
 * @method $this setOptionList(array $value)
 */
class Mage_Wishlist_Block_Customer_Wishlist_Item_Options extends Mage_Wishlist_Block_Abstract
{
    /**
     * List of product options rendering configurations by product type
     *
     * @var array
     */
    protected $_optionsCfg = ['default' => [
        'helper' => 'catalog/product_configuration',
        'template' => 'wishlist/options_list.phtml',
    ]];

    /**
     * Initialize block
     */
    public function __construct()
    {
        parent::__construct();
        Mage::dispatchEvent('product_option_renderer_init', ['block' => $this]);
    }

    /**
     * Adds config for rendering product type options
     *
     * @param string $productType
     * @param string $helperName
     * @param null|string $template
     * @return $this
     */
    public function addOptionsRenderCfg($productType, $helperName, $template = null)
    {
        $this->_optionsCfg[$productType] = ['helper' => $helperName, 'template' => $template];
        return $this;
    }

    /**
     * Get item options renderer config
     *
     * @param string $productType
     * @return array|null
     */
    public function getOptionsRenderCfg($productType)
    {
        if (isset($this->_optionsCfg[$productType])) {
            return $this->_optionsCfg[$productType];
        } elseif (isset($this->_optionsCfg['default'])) {
            return $this->_optionsCfg['default'];
        } else {
            return null;
        }
    }

    /**
     * Retrieve product configured options
     *
     * @return array
     */
    public function getConfiguredOptions()
    {
        $item = $this->getItem();
        $data = $this->getOptionsRenderCfg($item->getProduct()->getTypeId());
        if (empty($data['helper'])
            || !$this->helper($data['helper']) instanceof Mage_Catalog_Helper_Product_Configuration_Interface
        ) {
            Mage::throwException($this->__("Helper for wishlist options rendering doesn't implement required interface."));
        }

        return $this->helper($data['helper'])->getOptions($item);
    }

    /**
     * Retrieve block template
     *
     * @return string
     */
    public function getTemplate()
    {
        $template = parent::getTemplate();
        if ($template) {
            return $template;
        }

        $item = $this->getItem();

        if ($item instanceof Mage_Wishlist_Model_Item) {
            $data = $this->getOptionsRenderCfg($item->getProduct()->getTypeId());
            if (empty($data['template'])) {
                $data = $this->getOptionsRenderCfg('default');
            }
        }

        return empty($data['template']) ? '' : $data['template'];
    }

    /**
     * Render block html
     *
     * @return string
     */
    protected function _toHtml()
    {
        $this->setOptionList($this->getConfiguredOptions());

        return parent::_toHtml();
    }
}
