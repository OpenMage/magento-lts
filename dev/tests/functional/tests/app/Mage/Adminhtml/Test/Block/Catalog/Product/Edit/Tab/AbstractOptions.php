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

namespace Mage\Adminhtml\Test\Block\Catalog\Product\Edit\Tab;

use Magento\Mtf\Client\Element\SimpleElement as Element;
use Mage\Adminhtml\Test\Block\Widget\Tab;

/**
 * Parent class for all forms of product options.
 */
abstract class AbstractOptions extends Tab
{
    /**
     * Fills in the form of an array of input data.
     *
     * @param array $fields
     * @param Element $element [optional]
     * @return $this
     */
    public function fillOptions(array $fields, Element $element = null)
    {
        $element = $element === null ? $this->_rootElement : $element;
        $mapping = $this->dataMapping($fields);
        $this->_fill($mapping, $element);
        return $this;
    }

    /**
     * Getting options data form on the product form.
     *
     * @param array $fields [optional]
     * @param Element $element [optional]
     * @return $this
     */
    public function getOptions(array $fields = null, Element $element = null)
    {
        $element = $element === null ? $this->_rootElement : $element;
        $mapping = $this->dataMapping($fields);
        return $this->_getData($mapping, $element);
    }
}
