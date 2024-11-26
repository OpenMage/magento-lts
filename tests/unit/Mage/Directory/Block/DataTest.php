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

use Generator;
use Mage;
use Mage_Directory_Block_Data;
use Mage_Directory_Model_Resource_Country_Collection;
use Mage_Directory_Model_Resource_Region_Collection;
use PHPUnit\Framework\TestCase;

class DataTest extends TestCase
{
    public const TEST_STRING = '1234567890';

    public Mage_Directory_Block_Data $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = new Mage_Directory_Block_Data();
    }

    /**
     * @group Mage_Directory
     * @group Mage_Directory_Block
     */
    public function testGetCountryCollection(): void
    {
        $this->assertInstanceOf(Mage_Directory_Model_Resource_Country_Collection::class, $this->subject->getCountryCollection());
    }

    /**
     * @group Mage_Directory
     * @group Mage_Directory_Block
     */
    public function testGetRegionCollection(): void
    {
        $this->assertInstanceOf(Mage_Directory_Model_Resource_Region_Collection::class, $this->subject->getRegionCollection());
    }
}
