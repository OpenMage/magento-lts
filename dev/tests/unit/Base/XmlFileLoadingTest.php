<?php

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Base;

use PHPUnit\Framework\TestCase;
use SimpleXMLElement;
use XMLReader;

class XmlFileLoadingTest extends TestCase
{
    /**
     *
     * @dataProvider provideXmlFiles
     * @param string $filepath
     * @return void
     */
    public function testFileLoading(string $filepath): void
    {
        /** @var SimpleXMLElement $simplexml */
        $simplexml = simplexml_load_file(
            $filepath,
            SimpleXMLElement::class,
            LIBXML_PEDANTIC //not needed by OpenMage, but good to test more strictly
        );
        $this->assertNotEmpty($simplexml->asXML());
    }

    /**
     *
     * @dataProvider provideXmlFiles
     * @param string $filepath
     * @return void
     */
    public function testXmlReaderIsValid(string $filepath): void
    {
        /** @var XMLReader $xml */
        $xml = XMLReader::open($filepath);
        $xml->setParserProperty(XMLReader::VALIDATE, true);
        $this->assertTrue($xml->isValid());
    }

    /**
     * @return string[][]
     */
    public function provideXmlFiles(): array
    {
        // phpcs:ignore Ecg.Security.ForbiddenFunction.Found
        $root = realpath(__DIR__ . '/../../../../') . '/';

        return [
            'file from vendor directory' => [
                $root . 'vendor/shardj/zf1-future/library/Zend/Locale/Data/es_419.xml'
            ],
        ];
    }
}
