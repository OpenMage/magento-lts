<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Tests
 * @package    Tests_Functional
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Adminhtml\Test\Block\Cms\Wysiwyg;

use Magento\Mtf\Block\Block;
use Magento\Mtf\Client\Locator;

/**
 * System variable management block.
 */
class Config extends Block
{
    /**
     * Variable link selector.
     *
     * @var string
     */
    protected $variableSelector = '//a[contains(text(),"%s")]';

    /**
     * Select variable by name.
     *
     * @param string $variableName
     * @return void
     */
    public function selectVariable($variableName)
    {
        $this->_rootElement->find(sprintf($this->variableSelector, $variableName), Locator::SELECTOR_XPATH)->click();
    }
}
