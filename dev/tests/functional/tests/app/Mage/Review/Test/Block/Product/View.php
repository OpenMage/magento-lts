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

namespace Mage\Review\Test\Block\Product;

use Magento\Mtf\Client\ElementInterface;

/**
 * Product view block on the product page.
 */
class View extends \Mage\Catalog\Test\Block\Product\View
{
    /**
     * 'View review link' selector.
     *
     * @var string
     */
    protected $viewReviewLinkSelector = '.ratings a:nth-child(1)';

    /**
     * 'Add review link' selector.
     *
     * @var string
     */
    protected $addReviewLinkSelector = '.ratings a[href$="#review-form"]';

    /**
     * Get 'view review link'.
     *
     * @return ElementInterface
     */
    public function getViewReviewLink()
    {
        return $this->_rootElement->find($this->viewReviewLinkSelector);
    }

    /**
     * Get 'add review link'.
     *
     * @return ElementInterface
     */
    public function getAddReviewLink()
    {
        return $this->_rootElement->find($this->addReviewLinkSelector);
    }
}
