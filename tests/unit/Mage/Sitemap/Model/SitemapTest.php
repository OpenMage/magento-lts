<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @group Mage_Sitemap
 * @group Mage_Sitemap_Model
 */
declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Sitemap\Model;

use Mage;
use Mage_Sitemap_Model_Sitemap as Subject;
use PHPUnit\Framework\TestCase;

class SitemapTest extends TestCase
{
    public const SITEMAP_FILE = '???phpunit.sitemap.xml';

    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::getModel('sitemap/sitemap');
    }

    
    public function testGetPreparedFilename(): void
    {
        $mock = $this->getMockBuilder(Subject::class)
            ->setMethods(['getSitemapFilename'])
            ->getMock();

        $mock->method('getSitemapFilename')->willReturn('text.xml');
        $this->assertIsString($mock->getPreparedFilename());
    }

    /**
     * @group Mage_Sitemap
     * @group Mage_Sitemap
     * @group Mage_Sitemap_Model
     * @group Mage_Sitemap_Model
     * @todo  test content of xml
     * @todo  test validation
     */
    public function testGenerateXml(): void
    {
        $mock = $this->getMockBuilder(Subject::class)
            ->setMethods(['isDeleted']) # do not save to DB
            ->setMethods(['getSitemapFilename'])
            ->getMock();

        $mock->method('isDeleted')->willReturn(true);
        $mock->method('getSitemapFilename')->willReturn(self::SITEMAP_FILE);
        $result = $mock->generateXml();
        $this->assertInstanceOf(Subject::class, $result);
        $this->assertFileExists(self::SITEMAP_FILE);
        unlink(self::SITEMAP_FILE);
    }
}
