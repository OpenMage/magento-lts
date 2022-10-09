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

namespace Mage\Review\Test\TestStep;

use Mage\Rating\Test\Page\Adminhtml\RatingEdit;
use Mage\Rating\Test\Page\Adminhtml\RatingIndex;
use Magento\Mtf\TestStep\TestStepInterface;

/**
 * Delete all ratings.
 */
class DeleteAllRatingsStep implements TestStepInterface
{
    /**
     * Backend rating grid page.
     *
     * @var RatingIndex
     */
    protected $ratingIndex;

    /**
     * Backend rating edit page.
     *
     * @var RatingEdit
     */
    protected $ratingEdit;

    /**
     * @constructor
     * @param RatingEdit $ratingEdit
     * @param RatingIndex $ratingIndex
     */
    public function __construct(RatingEdit $ratingEdit, RatingIndex $ratingIndex)
    {
        $this->ratingEdit = $ratingEdit;
        $this->ratingIndex = $ratingIndex;
    }

    /**
     * Delete all ratings.
     *
     * @return void
     */
    public function run()
    {
        $this->ratingIndex->open();
        while ($this->ratingIndex->getRatingGrid()->isFirstRowVisible()) {
            $this->ratingIndex->getRatingGrid()->openFirstRow();
            $this->ratingEdit->getPageActions()->deleteAndAcceptAlert();
        }
    }
}
