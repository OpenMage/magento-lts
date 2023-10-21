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

namespace Mage\Catalog\Test\Block\Product\View;

use Magento\Mtf\Block\Form;

/**
 * Form of grouped product item.
 */
class GroupedItemForm extends Form
{
    /**
     * Fill grouped product item.
     *
     * @param array $option
     * @return void
     */
    public function fillOption(array $option)
    {
        unset($option['name']);
        $mapping = $this->dataMapping($option);
        $this->_fill($mapping);
    }
}
