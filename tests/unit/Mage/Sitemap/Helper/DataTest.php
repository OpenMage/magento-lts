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

namespace OpenMage\Tests\Unit\Mage\Sitemap\Helper;

use Mage;
use Mage_Sitemap_Helper_Data;
use PHPUnit\Framework\TestCase;

class DataTest extends TestCase
{
    public Mage_Sitemap_Helper_Data $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::helper('sitemap/data');
    }

    /**
     * @group Mage_Sitemap
     * @group Mage_Sitemap_Helper
     */
    public function testIsCategoryEnabled(): void
    {
        $this->assertTrue($this->subject->isCategoryEnabled());
    }

    /**
     * @group Mage_Sitemap
     * @group Mage_Sitemap_Helper
     */
    public function testIsCmsPageEnabled(): void
    {
        $this->assertTrue($this->subject->isCmsPageEnabled());
    }

    /**
     * @group Mage_Sitemap
     * @group Mage_Sitemap_Helper
     */
    public function testIsProductEnabled(): void
    {
        $this->assertTrue($this->subject->isProductEnabled());
    }
}
