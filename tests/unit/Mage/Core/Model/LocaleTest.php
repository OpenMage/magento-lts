<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   OpenMage
 * @package    OpenMage_Tests
 * @copyright  Copyright (c) 2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Model;

use Mage;
use Mage_Core_Model_Locale as Subject;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Catalog\Model\LocaleTrait;
use OpenMage\Tests\Unit\OpenMageTest;

class LocaleTest extends OpenMageTest
{
    use LocaleTrait;

    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('core/locale');
    }

    /**
     * @dataProvider provideGetNumberData
     * @param string|float|int $value
     *
     * @group Mage_Core
     */
    public function testGetNumber(?float $expectedResult, $value): void
    {
        static::assertSame($expectedResult, self::$subject->getNumber($value));
    }
}
