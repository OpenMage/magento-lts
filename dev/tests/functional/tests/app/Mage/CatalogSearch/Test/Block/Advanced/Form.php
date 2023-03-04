<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category    Tests
 * @package     Tests_Functional
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\CatalogSearch\Test\Block\Advanced;

/**
 * Advanced search form.
 */
class Form extends \Magento\Mtf\Block\Form
{
    /**
     * Field selector.
     *
     * @var string
     */
    protected $fieldSelector = '#advanced-search-list li';

    /**
     * Label element selector.
     *
     * @var string
     */
    protected $labelSelector = 'label';

    /**
     * Get form fields.
     *
     * @return array
     */
    public function getFormLabels()
    {
        $labels = [];
        $elements = $this->_rootElement->getElements($this->fieldSelector);
        foreach ($elements as $element) {
            $labels[] = $element->find($this->labelSelector)->getText();
        }
        return $labels;
    }
}
