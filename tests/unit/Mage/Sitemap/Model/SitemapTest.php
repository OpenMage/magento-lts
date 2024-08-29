<?php

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Sitemap\Model;

use Mage;
use Mage_Sitemap_Model_Sitemap;
use PHPUnit\Framework\TestCase;

class SitemapTest extends TestCase
{
    /**
     * @var Mage_Sitemap_Model_Sitemap
     */
    public Mage_Sitemap_Model_Sitemap $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::getModel('sitemap/sitemap');
    }

    public function testGenerateXml(): void
    {
        $mock = $this->getMockBuilder(Mage_Sitemap_Model_Sitemap::class)
            ->setMethods(['getSitemapFilename'])
            ->getMock();

        $mock->expects($this->any())->method('getSitemapFilename')->willReturn('text.xml');
        $this->assertInstanceOf(Mage_Sitemap_Model_Sitemap::class, $mock->generateXml());
    }
}
