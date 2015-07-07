<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Tests
 * @package     Tests_Functional
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Review\Test\Constraint;

use Magento\Mtf\Constraint\AbstractConstraint;
use Magento\Mtf\ObjectManager;
use Mage\Catalog\Test\Page\Product\CatalogProductView;

/**
 * Assert that product don't have a review on product page.
 */
class AssertProductReviewIsAbsentOnProductPage extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Verify message for assert.
     */
    const NO_REVIEW_LINK_TEXT = 'Be the first to review this product';

    /**
     * Catalog product view page.
     *
     * @var CatalogProductView
     */
    protected $catalogProductView;

    /**
     * @constructor
     * @param ObjectManager $objectManager
     * @param CatalogProductView $catalogProductView
     */
    public function __construct(ObjectManager $objectManager, CatalogProductView $catalogProductView)
    {
        parent::__construct($objectManager);
        $this->catalogProductView = $catalogProductView;
    }

    /**
     * Assert that product doesn't have a review on product page.
     *
     * @return void
     */
    public function processAssert()
    {
        $this->catalogProductView->getViewBlock()->openCustomInformationTab('Reviews');

        \PHPUnit_Framework_Assert::assertFalse(
            $this->catalogProductView->getReviewsBlock()->isVisibleReviewItems(),
            'No reviews below the form required.'
        );
        \PHPUnit_Framework_Assert::assertEquals(
            self::NO_REVIEW_LINK_TEXT,
            trim($this->catalogProductView->getReviewsBlock()->getAddReviewLink()->getText()),
            sprintf('"%s" link is not available', self::NO_REVIEW_LINK_TEXT)
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Product do not have a review on product page.';
    }
}
