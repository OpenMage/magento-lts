<?php

/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @dataProvider provideSplitSku
 * @group Mage_Catalog
 * @group Mage_Catalog_Helper
 */
declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Catalog\Helper;

use Generator;
use Mage;
use Mage_Catalog_Helper_Data as Subject;
use Mage_Catalog_Model_Template_Filter;
use PHPUnit\Framework\TestCase;

class DataTest extends TestCase
{
    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::helper('catalog');
    }


    public function testSplitSku($expectedResult, string $sku, int $length = 30): void
    {
        $this->assertSame($expectedResult, $this->subject->splitSku($sku, $length));
    }

    public function provideSplitSku(): Generator
    {
        yield 'test #1' => [
            [
                '100',
            ],
            '100',
        ];
        yield 'test #2 w/ length' => [
            [
                '10',
                '0',
            ],
            '100',
            2,
        ];
    }

    /**
     * @group Mage_Catalog
     * @group Mage_Catalog_Helper
     */
    public function testShouldSaveUrlRewritesHistory(): void
    {
        $this->assertIsBool($this->subject->shouldSaveUrlRewritesHistory());
    }

    /**
     * @group Mage_Catalog
     * @group Mage_Catalog_Helper
     */
    public function testIsUsingStaticUrlsAllowed(): void
    {
        $this->assertIsBool($this->subject->isUsingStaticUrlsAllowed());
    }

    /**
     * @group Mage_Catalog
     * @group Mage_Catalog_Helper
     */
    public function testIsUrlDirectivesParsingAllowed(): void
    {
        $this->assertIsBool($this->subject->isUrlDirectivesParsingAllowed());
    }

    /**
     * @group Mage_Catalog
     * @group Mage_Catalog_Helper
     */
    public function testGetPageTemplateProcessor(): void
    {
        $this->assertInstanceOf(Mage_Catalog_Model_Template_Filter::class, $this->subject->getPageTemplateProcessor());
    }

    /**
     * @group Mage_Catalog
     * @group Mage_Catalog_Helper
     */
    public function testGetOldFieldMap(): void
    {
        $this->assertSame([], $this->subject->getOldFieldMap());
    }
}
