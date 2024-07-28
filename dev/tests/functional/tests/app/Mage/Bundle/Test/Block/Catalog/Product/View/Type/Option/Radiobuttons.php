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

namespace Mage\Bundle\Test\Block\Catalog\Product\View\Type\Option;

use Mage\Bundle\Test\Block\Catalog\Product\View\Type\Option;

/**
 * Bundle option radio button type.
 */
class Radiobuttons extends Option
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
        $mapping['name']['selector'] = str_replace('%product_name%', $data['name'], $mapping['name']['selector']);
        $mapping['name']['value'] = 'Yes';
        $this->_fill($mapping);
    }
}
