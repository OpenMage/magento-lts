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

namespace Mage\Adminhtml\Test\Block\Catalog\Product\Attribute\Edit\Tab\Options;

use Magento\Mtf\Block\Form;

/**
 * Form "Manage Options" on tab "Manage Label/Options".
 */
class Option extends Form
{
    /**
     * Fill attribute option.
     *
     * @param array $fields
     * @return void
     */
    public function fillOption(array $fields)
    {
        $data = $this->dataMapping($fields);
        $this->_fill($data);
    }

    /**
     * Get attribute option.
     *
     * @param array $value
     * @return array
     */
    public function getOption(array $value)
    {
        $mapping = $this->dataMapping($value);
        $options = $this->_getData($mapping);

        return $options;
    }
}
