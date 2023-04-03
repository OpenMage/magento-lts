<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 *
 * @category    Tests
 * @package     Tests_Functional
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Bundle\Test\Block\Catalog\Product\View\Type;

use Magento\Mtf\Block\Form;

/**
 * Bundle option.
 */
class Option extends Form
{
    /**
     * Set data in bundle option.
     *
     * @param array $data
     * @return void
     */
    public function fillOption(array $data)
    {
        $mapping = $this->dataMapping($data);
        $this->_fill($mapping);
    }
}
