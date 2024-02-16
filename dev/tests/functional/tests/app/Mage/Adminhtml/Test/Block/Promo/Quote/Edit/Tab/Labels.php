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

namespace Mage\Adminhtml\Test\Block\Promo\Quote\Edit\Tab;

use Mage\Adminhtml\Test\Block\Widget\Tab;
use Magento\Mtf\Client\Element\SimpleElement as Element;

/**
 * Backend sales rule label tab.
 */
class Labels extends Tab
{
    /**
     * Store label field name.
     */
    const STORE_LABEL_NAME = '[name="store_labels[%s]"]';

    /**
     * Fill data to labels fields on labels tab.
     *
     * @param array $fields
     * @param Element|null $element
     * @return $this
     */
    public function fillFormTab(array $fields, Element $element = null)
    {
        if (isset($fields['store_labels'])) {
            $count = 0;
            foreach ($fields['store_labels']['value'] as $storeLabel) {
                $element->find(sprintf(self::STORE_LABEL_NAME, $count))->setValue($storeLabel);
                ++$count;
            }
        }

        return $this;
    }

    /**
     * Get data of labels tab.
     *
     * @param array|null $fields
     * @param Element|null $element
     * @return array
     */
    public function getDataFormTab($fields = null, Element $element = null)
    {
        $storeLabels = [];
        $count = 0;
        $field = $this->_rootElement->find(sprintf(self::STORE_LABEL_NAME, $count));
        while ($field->isVisible()) {
            $fieldValue = $field->getValue();
            if ($fieldValue != '') {
                $storeLabels[$count] = $fieldValue;
            }
            ++$count;
            $field = $this->_rootElement->find(sprintf(self::STORE_LABEL_NAME, $count));
        }

        return ['store_labels' => $storeLabels];
    }
}
