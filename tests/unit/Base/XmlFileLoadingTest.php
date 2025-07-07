<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Base;

use Generator;
use PHPUnit\Framework\TestCase;
use SimpleXMLElement;
use XMLReader;

final class XmlFileLoadingTest extends TestCase
{
    /**
     * @dataProvider provideXmlFiles
     */
    public function testFileLoading(string $filepath): void
    {
        /** @var SimpleXMLElement $simplexml */
        $simplexml = simplexml_load_file(
            $filepath,
            SimpleXMLElement::class,
            LIBXML_PEDANTIC, //not needed by OpenMage, but good to test more strictly
        );
        static::assertNotEmpty($simplexml->asXML());
    }

    /**
     * @dataProvider provideXmlFiles
     */
    public function testXmlReaderIsValid(string $filepath): void
    {
        /** @var XMLReader $xml */
        $xml = XMLReader::open($filepath);
        $xml->setParserProperty(XMLReader::VALIDATE, true);
        static::assertTrue($xml->isValid());
    }

    public function provideXmlFiles(): Generator
    {
        $root = realpath(__DIR__ . '/../../../') . '/';

        yield 'file from vendor directory' => [
            $root . 'vendor/shardj/zf1-future/library/Zend/Locale/Data/es_419.xml',
        ];
    }
}
