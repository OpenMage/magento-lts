<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @group Base
 * @dataProvider provideXmlFiles
 */
declare(strict_types=1);

namespace OpenMage\Tests\Unit\Base;

use Generator;
use PHPUnit\Framework\TestCase;
use SimpleXMLElement;
use XMLReader;

class XmlFileLoadingTest extends TestCase
{
    
    public function testFileLoading(string $filepath): void
    {
        /** @var SimpleXMLElement $simplexml */
        $simplexml = simplexml_load_file(
            $filepath,
            SimpleXMLElement::class,
            LIBXML_PEDANTIC, //not needed by OpenMage, but good to test more strictly
        );
        $this->assertNotEmpty($simplexml->asXML());
    }

    
    public function testXmlReaderIsValid(string $filepath): void
    {
        /** @var XMLReader $xml */
        $xml = XMLReader::open($filepath);
        $xml->setParserProperty(XMLReader::VALIDATE, true);
        $this->assertTrue($xml->isValid());
    }

    public function provideXmlFiles(): Generator
    {
        $root = realpath(__DIR__ . '/../../../') . '/';

        yield 'file from vendor directory' => [
            $root . 'vendor/shardj/zf1-future/library/Zend/Locale/Data/es_419.xml',
        ];
    }
}
