<?php
declare(strict_types=1);

namespace OpenMage\Tests\Unit\Base;

use PHPUnit\Framework\TestCase;

class XmlFileLoadingTest  extends TestCase
{

    public function provideXmlFiles(): array
    {
        $root = realpath(__DIR__ . '/../../../../') . '/';

        $result = [];
        $result[] = [
            $root . 'vendor/shardj/zf1-future/library/Zend/Locale/Data/es_419.xml'
        ];

        return $result;
    }

    /**
     *
     * @dataProvider provideXmlFiles
     * @param $filepath
     */
    public function testFileLoading($filepath): void
    {
        //$simplexml = new \SimpleXMLElement(file_get_contents($filepath));
        $simplexml = simplexml_load_file(
            $filepath,
            null,
            LIBXML_PEDANTIC //not needed by OpenMage, but good to test more strictly
        );
        $this->assertNotEmpty($simplexml->asXML());
    }

    /**
     *
     * @dataProvider provideXmlFiles
     * @param $filepath
     */
    public function testXmlReaderIsValid($filepath): void
    {
        $xml = \XMLReader::open($filepath);
        $xml->setParserProperty(\XMLReader::VALIDATE, true);
        $this->assertTrue($xml->isValid());
    }
}
