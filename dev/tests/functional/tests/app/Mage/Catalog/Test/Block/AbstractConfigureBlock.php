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
 * @category    Tests
 * @package     Tests_Functional
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Catalog\Test\Block;

use Magento\Mtf\Block\Form;
use Mage\Catalog\Test\Block\Product\View\CustomOptions;

/**
 * Product configure form.
 */
abstract class AbstractConfigureBlock extends Form
{
    /**
     * This method returns the custom options block.
     *
     * @return CustomOptions
     */
    protected function getCustomOptionsBlock()
    {
        return $this->blockFactory->create(
            'Mage\Catalog\Test\Block\Product\View\CustomOptions',
            ['element' => $this->_rootElement]
        );
    }

    /**
     * Set quantity.
     *
     * @param int $qty
     * @return void
     */
    abstract public function setQty($qty);

    /**
     * Replace index fields to name fields in checkout data
     *
     * @param array $options
     * @param array $checkoutData
     * @return array
     */
    protected function prepareCheckoutData(array $options, array $checkoutData)
    {
        $result = [];

        foreach ($checkoutData as $checkoutOption) {
            $attribute = str_replace('attribute_key_', '', $checkoutOption['title']);
            $option = str_replace('option_key_', '', $checkoutOption['value']);

            if (isset($options[$attribute])) {

                $result[] = [
                    'type' => strtolower(preg_replace('/[^a-z]/i', '', explode('/', $options[$attribute]['type']))[1]),
                    'title' => isset($options[$attribute]['title'])
                            ? $options[$attribute]['title']
                            : $attribute,
                    'value' => isset($options[$attribute]['options'][$option]['title'])
                            ? $options[$attribute]['options'][$option]['title']
                            : $option
                ];
            }
        }

        return $result;
    }
}
