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

namespace OpenMage\Tests\Unit\Mage\Sitemap\Model;

use Mage;
use Mage_Sitemap_Model_Sitemap;
use PHPUnit\Framework\TestCase;

class SitemapTest extends TestCase
{
    public Mage_Sitemap_Model_Sitemap $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::getModel('sitemap/sitemap');
    }

    /**
     * @group Mage_Sitemap
     * @group Mage_Sitemap_Model
     */
    public function testGetPreparedFilename(): void
    {
        $mock = $this->getMockBuilder(Mage_Sitemap_Model_Sitemap::class)
            ->setMethods(['getSitemapFilename'])
            ->getMock();

        $mock->method('getSitemapFilename')->willReturn('text.xml');
        $this->assertIsString($mock->getPreparedFilename());
    }

    /**
     * @group Mage_Sitemap
     * @group Mage_Sitemap_Model
     */
    public function testGenerateXml(): void
    {
        $mock = $this->getMockBuilder(Mage_Sitemap_Model_Sitemap::class)
            ->setMethods(['getSitemapFilename'])
            ->getMock();

        $mock->method('getSitemapFilename')->willReturn('text.xml');
        $this->assertInstanceOf(Mage_Sitemap_Model_Sitemap::class, $mock->generateXml());
    }
}
