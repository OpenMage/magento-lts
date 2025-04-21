<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 * @copyright  Copyright (c) 2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Admin\Helper;

use Mage;
use Mage_Admin_Helper_Data as Subject;
use PHPUnit\Framework\TestCase;

class DataTest extends TestCase
{
    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::helper('admin/data');
    }

    /**
     * @covers Mage_Admin_Helper_Data::generateResetPasswordLinkToken()
     * @group Mage_Admin
     * @group Mage_Admin_Helper
     */
    public function testGenerateResetPasswordLinkToken(): void
    {
        $this->assertIsString($this->subject->generateResetPasswordLinkToken());
    }

    /**
     * @covers Mage_Admin_Helper_Data::getResetPasswordLinkExpirationPeriod()
     * @group Mage_Admin
     * @group Mage_Admin_Helper
     */
    public function testGetResetPasswordLinkExpirationPeriod(): void
    {
        $this->assertIsInt($this->subject->getResetPasswordLinkExpirationPeriod());
    }
}
