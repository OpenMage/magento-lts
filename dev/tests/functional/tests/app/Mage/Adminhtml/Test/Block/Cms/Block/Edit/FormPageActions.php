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

namespace Mage\Adminhtml\Test\Block\Cms\Block\Edit;

/**
 * Form page actions block for cms page.
 */
class FormPageActions extends \Mage\Adminhtml\Test\Block\FormPageActions
{
    /**
     * Get Cms Block id.
     *
     * @return int
     * @throws \Exception
     */
    public function getBlockId()
    {
        $attribute =  $this->_rootElement->find($this->deleteButton)->getAttribute('onclick');

        preg_match('~http[^\s]*\/block_id\/(\d+)~', $attribute, $matches);
        if (empty($matches)) {
            throw new \Exception('Cannot find Block id');
        }

        return $matches[1];
    }
}
