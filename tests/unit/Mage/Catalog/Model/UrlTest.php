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

namespace OpenMage\Tests\Unit\Mage\Catalog\Model;

use Generator;
use Mage;
use Mage_Catalog_Model_Url;
use PHPUnit\Framework\TestCase;
use Symfony\Component\String\Slugger\AsciiSlugger;

class UrlTest extends TestCase
{
    public const TEST_STRING = 'a & B, x%, ä, ö, ü';

    public Mage_Catalog_Model_Url $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::getModel('catalog/url');
    }

    /**
     * @dataProvider provideFormatUrlKey
     * @group Mage_Catalog
     * @group Mage_Catalog_Model
     */
    public function testFormatUrlKey($expectedResult, string $locale): void
    {
        $this->subject->setLocale($locale);
        $this->assertSame($expectedResult, $this->subject->formatUrlKey(self::TEST_STRING));
    }
    public function provideFormatUrlKey(): Generator
    {
        yield 'de_DE' => [
            'a-und-b-x-prozent-ae-oe-ue',
            'de_DE',
        ];
        yield 'en_US' => [
            'a-and-b-x-percent-a-o-u',
            'en_US',
        ];
        yield 'fr_FR' => [
            'a-et-b-x-pour-cent-a-o-u',
            'fr_FR',
        ];
    }

    /**
     * @group Mage_Catalog
     * @group Mage_Catalog_Model
     */
    public function testGetSlugger(): void
    {
        $this->assertInstanceOf(AsciiSlugger::class, $this->subject->getSlugger());
    }

    /**
     * @dataProvider provideGetSluggerConfig
     * @group Mage_Catalog
     * @group Mage_Catalog_Model
     */
    public function testGetSluggerConfig($expectedResult, string $locale): void
    {
        $this->assertSame($expectedResult, $this->subject->getSluggerConfig($locale));
    }

    public function provideGetSluggerConfig(): Generator
    {
        yield 'de_DE' => [
            ['de_DE' => [
                '@' => 'at',
                '\u00a9' => 'c',
                '\u00ae' => 'r',
                '\u2122' => 'tm',
                '%' => 'prozent',
                '&' => 'und',
            ]],
            'de_DE'
        ];
        yield 'en_US' => [
            ['en_US' => [
                '@' => 'at',
                '\u00a9' => 'c',
                '\u00ae' => 'r',
                '\u2122' => 'tm',
                '%' => 'percent',
                '&' => 'and',
            ]],
            'en_US'
        ];
        yield 'fr_FR' => [
            ['fr_FR' => [
                '@' => 'at',
                '\u00a9' => 'c',
                '\u00ae' => 'r',
                '\u2122' => 'tm',
                '%' => 'pour cent',
                '&' => 'et',
            ]],
            'fr_FR'
        ];
    }
}
