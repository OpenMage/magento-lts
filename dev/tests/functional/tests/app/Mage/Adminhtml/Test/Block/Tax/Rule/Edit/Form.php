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
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Adminhtml\Test\Block\Tax\Rule\Edit;

use Magento\Mtf\Client\Locator;

/**
 * Form for tax rule creation.
 */
class Form extends \Magento\Mtf\Block\Form
{
    /**
     * Tax rates selector.
     *
     * @var string
     */
    protected $taxRate = '[name^="tax_rate"]';

    /**
     * Get all available tax rates.
     *
     * @return array
     */
    public function getAvailableTaxRates()
    {
        return $this->_rootElement->find($this->taxRate, Locator::SELECTOR_CSS, 'multiselectlist')->getAllValues();
    }

    /**
     * Check whether tax rate is visible in the list.
     *
     * @param string $value
     * @return bool
     */
    public function isTaxRateAvailable($value)
    {
        return $this->_rootElement->find($this->taxRate, Locator::SELECTOR_CSS, 'multiselectlist')
            ->isValueVisible($value);
    }

}
