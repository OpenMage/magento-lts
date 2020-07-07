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
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Install\Test\Constraint;

use Mage\Install\Test\Page\Install;
use Magento\Mtf\Constraint\AbstractConstraint;

/**
 * Check that agreement text present on Terms & Agreement page during install.
 */
class AssertAgreementTextPresent extends AbstractConstraint
{
    /**
     * Part of license agreement text.
     */
    const LICENSE_AGREEMENT_TEXT = 'Open Software License';

    /**
     * Assert that part of license agreement text is present on Terms & Agreement page.
     *
     * @param Install $installPage
     * @return void
     */
    public function processAssert(Install $installPage)
    {
        \PHPUnit_Framework_Assert::assertContains(
            self::LICENSE_AGREEMENT_TEXT,
            $installPage->getLicenseBlock()->getLicense(),
            'License agreement text is absent.'
        );
    }

    /**
     * Returns a string representation of successful assertion.
     *
     * @return string
     */
    public function toString()
    {
        return "License agreement text is present on Terms & Agreement page.";
    }
}
