<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Dataflow\Model;

use Mage;
use Mage_Core_Exception;
use Mage_Dataflow_Model_Profile as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Dataflow\Model\ProfileTrait;
use SimpleXMLElement;

final class ProfileTest extends OpenMageTest
{
    use ProfileTrait;

    /**
     * Values typed into the profile wizard reach the generated actions XML as data:
     * they can neither close their element or CDATA section nor add actions.
     *
     * @dataProvider provideParseGuiDataData
     * @param array<string, mixed>  $profileData
     * @param array<string, string> $expectedVars var name => value of the first such var
     * @param array<string, string> $expectedMaps map name => mapped field
     * @group Model
     */
    public function testParseGuiDataEscapesWizardValues(array $profileData, int $expectedActions, array $expectedVars, array $expectedMaps): void
    {
        /** @var Subject $subject */
        $subject = Mage::getModel('dataflow/profile');
        $subject->setData($profileData);

        $subject->_parseGuiData();

        $xml = new SimpleXMLElement('<profile>' . $subject->getActionsXml() . '</profile>');
        self::assertCount($expectedActions, $xml->action);
        foreach ($expectedVars as $name => $value) {
            $vars = $xml->xpath('//var[@name=' . $this->xpathLiteral($name) . ']');
            self::assertNotEmpty($vars, "var {$name} missing");
            self::assertSame($value, (string) $vars[0]);
        }

        $maps = [];
        foreach ($xml->xpath('//map') ?? [] as $map) {
            $maps[(string) $map['name']] = (string) $map;
        }

        self::assertSame($expectedMaps, $maps);
    }

    /**
     * @dataProvider provideParseGuiDataInvalidData
     * @param array<string, mixed> $profileData
     * @group Model
     */
    public function testParseGuiDataRejectsValuesTheWizardDoesNotOffer(array $profileData): void
    {
        /** @var Subject $subject */
        $subject = Mage::getModel('dataflow/profile');
        $subject->setData($profileData);

        $this->expectException(Mage_Core_Exception::class);
        $subject->_parseGuiData();
    }

    private function xpathLiteral(string $value): string
    {
        return str_contains($value, "'") ? '"' . $value . '"' : "'" . $value . "'";
    }
}
