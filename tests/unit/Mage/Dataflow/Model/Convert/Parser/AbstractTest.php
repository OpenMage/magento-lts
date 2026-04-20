<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Dataflow\Model\Convert\Parser;

use Mage_Dataflow_Model_Batch;
use Mage_Dataflow_Model_Batch_Export;
use Mage_Dataflow_Model_Batch_Import;
use Mage_Dataflow_Model_Convert_Parser_Abstract as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Dataflow\Model\Convert\Parser\AbstractTrait;

final class AbstractTest extends OpenMageTest
{
    use AbstractTrait;

    private static Subject $subject;

    protected function setUp(): void
    {
        parent::setUp();
        self::$subject = $this->createMock(Subject::class);
    }

    /**
     * @group Model
     */
    public function testGetBatchModel(): void
    {
        self::assertInstanceOf(Mage_Dataflow_Model_Batch::class, self::$subject->getBatchModel());
    }

    /**
     * @group Model
     */
    public function testGetBatchExportModel(): void
    {
        self::assertInstanceOf(Mage_Dataflow_Model_Batch_Export::class, self::$subject->getBatchExportModel());
    }

    /**
     * @group Model
     */
    public function testGetBatchImportModel(): void
    {
        self::assertInstanceOf(Mage_Dataflow_Model_Batch_Import::class, self::$subject->getBatchImportModel());
    }

    /**
     * @dataProvider provideGetCopyFile
     */
    public function testGetCopyFile(string $expectedResult, string $files): void
    {
        self::assertSame($expectedResult, self::$subject->getCopyFile($files));
    }
}
