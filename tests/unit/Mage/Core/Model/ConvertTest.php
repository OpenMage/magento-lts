<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Model;

use Mage;
use Mage_Core_Exception;
use Mage_Core_Model_Convert as Subject;
use Mage_Dataflow_Model_Convert_Adapter_Io;
use Mage_Dataflow_Model_Convert_Exception;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Core\Model\ConvertTrait;
use Override;

/**
 * GHSA-j64x-5vpp-m5pm
 */
final class ConvertTest extends OpenMageTest
{
    use ConvertTrait;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();
        ConvertTestConstructorProbe::$constructed = false;
    }

    /**
     * @dataProvider provideNonContainerTypes
     */
    public function testActionWithNonContainerTypeIsRefusedBeforeConstruction(string $type): void
    {
        try {
            $this->importProfile('<action type="' . $type . '" method="probe"/>');
            self::fail('Expected the profile to be refused');
        } catch (Mage_Core_Exception $exception) {
            self::assertSame('Invalid action type in profile actions XML.', $exception->getMessage());
        }

        self::assertFalse(ConvertTestConstructorProbe::$constructed);
    }

    public function testContainerWithNonContainerTypeIsRefusedBeforeConstruction(): void
    {
        $xml = '<convert version="1.0"><container name="x" type="' . ConvertTestConstructorProbe::class . '"/></convert>';

        try {
            $this->getSubject()->importXml($xml);
            self::fail('Expected the container to be refused');
        } catch (Mage_Core_Exception) {
        }

        self::assertFalse(ConvertTestConstructorProbe::$constructed);
    }

    /**
     * @dataProvider provideContainerTypes
     */
    public function testContainerTypesAreStillAccepted(string $type): void
    {
        $profile = $this->importProfile('<action type="' . $type . '" method="load"/>');

        $actions = (fn() => $this->_actions)->call($profile);
        self::assertInstanceOf(Mage_Dataflow_Model_Convert_Adapter_Io::class, $actions[0]->getContainer());
    }

    public function testUnknownMethodStopsTheProfileInsteadOfAFatalError(): void
    {
        $profile = $this->importProfile('<action type="dataflow/convert_adapter_io" method="doesNotExist"/>');

        $profile->run();

        $fatal = array_values(array_filter(
            $profile->getExceptions(),
            fn($exception) => $exception->getLevel() === Mage_Dataflow_Model_Convert_Exception::FATAL,
        ));
        self::assertCount(2, $fatal);
        self::assertSame('Unable to run action method: doesNotExist', $fatal[1]->getMessage());
    }

    public static function provideNonContainerTypes(): array
    {
        return [
            'arbitrary class' => [ConvertTestConstructorProbe::class],
            'varien object' => ['Varien_Object'],
            'non-container model alias' => ['core/store'],
            'unknown class' => ['No_Such_Class'],
            'invalid class name' => ['../../etc/passwd'],
            'empty' => [''],
        ];
    }

    public static function provideContainerTypes(): array
    {
        return [
            'model alias' => ['dataflow/convert_adapter_io'],
            'class name' => ['Mage_Dataflow_Model_Convert_Adapter_Io'],
        ];
    }

    private function importProfile(string $actionsXml): \Mage_Dataflow_Model_Convert_Profile
    {
        $xml = '<convert version="1.0"><profile name="default">' . $actionsXml . '</profile></convert>';
        return $this->getSubject()->importXml($xml)->getProfile('default');
    }

    private function getSubject(): Subject
    {
        return Mage::getModel('core/convert');
    }
}

final class ConvertTestConstructorProbe
{
    public static bool $constructed = false;

    public function __construct()
    {
        self::$constructed = true;
    }

    public function probe(): void {}
}
