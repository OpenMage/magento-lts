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

namespace Mage\Downloadable\Test\Block\Checkout\Onepage\Review;

/**
 * Credit Memo Items block for downloadable product on Credit Memo new page.
 */
class Items extends \Mage\Checkout\Test\Block\Onepage\Review\Items
{
    /**
     * Item block class.
     *
     * @var string
     */
    protected $classItemBlock = 'Mage\Downloadable\Test\Block\Checkout\Onepage\Review\Items\Product';
}
