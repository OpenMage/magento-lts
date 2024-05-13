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

namespace Mage\Theme\Test\Block;

use Magento\Mtf\Block\Block;

/**
 * Page Links block.
 */
class Links extends Block
{
    /**
     * Link selector locator.
     *
     * @var string
     */
    protected $linkSelector = 'a[title^="%s"]';

    /**
     * Open Link by title.
     *
     * @param string $linkTitle
     * @return void
     */
    public function openLink($linkTitle)
    {
        $this->_rootElement->find(sprintf($this->linkSelector, $linkTitle))->click();
    }

    /**
     * Check is link is visible.
     *
     * @param string $linkTitle
     * @return bool
     */
    public function isLinkVisible($linkTitle)
    {
        return $this->_rootElement->find(sprintf($this->linkSelector, $linkTitle))->isVisible();
    }
}
