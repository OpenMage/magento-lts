<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   OpenMage
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Model;

use Mage;
use Mage_Core_Model_Locale as Subject;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Catalog\Model\LocaleTrait;
use PHPUnit\Framework\TestCase;

class LocaleTest extends TestCase
{
    use LocaleTrait;

    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::getModel('core/locale');
    }

    /**
     * @dataProvider provideGetNumberData
     * @param string|float|int $value
     *
     * @group Mage_Core
     */
    public function testGetNumber(?float $expectedResult, $value): void
    {
        $this->assertSame($expectedResult, $this->subject->getNumber($value));
    }
}
