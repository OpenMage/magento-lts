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

namespace Mage\Adminhtml\Test\Block\Catalog\Category;

use Magento\Mtf\Block\Block;

/**
 * Category content block.
 */
class Content extends Block
{
    /**
     * Access denied message locator.
     *
     * @var string
     */
    protected $accessDenied = '.page-heading';

    /**
     * Check access denied message.
     *
     * @return bool
     */
    public function checkAccessDeniedMessage()
    {
        return $this->_rootElement->find($this->accessDenied)->isVisible();
    }
}
