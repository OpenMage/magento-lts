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

namespace OpenMage\Tests\Unit\Mage\Directory\Block;

use Mage;
use Mage_Directory_Block_Data as Subject;
use Mage_Directory_Model_Resource_Country_Collection;
use Mage_Directory_Model_Resource_Region_Collection;
use OpenMage\Tests\Unit\OpenMageTest;

class DataTest extends OpenMageTest
{
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = new Subject();
    }

    /**
     * @group Block
     */
    public function testGetCountryCollection(): void
    {
        static::assertInstanceOf(Mage_Directory_Model_Resource_Country_Collection::class, self::$subject->getCountryCollection());
    }

    /**
     * @group Block
     */
    public function testGetRegionCollection(): void
    {
        static::assertInstanceOf(Mage_Directory_Model_Resource_Region_Collection::class, self::$subject->getRegionCollection());
    }
}
