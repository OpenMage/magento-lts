<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Index\Model;

use Mage;
use Mage_Core_Exception;
use Mage_Index_Model_Process as Subject;
use Mage_Index_Model_Resource_Event_Collection;
use OpenMage\Tests\Unit\OpenMageTest;

final class ProcessTest extends OpenMageTest
{
    public const INDEXER_MODEL_IS_NOT_DEFINED = 'Indexer model is not defined.';

    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('index/process');
    }

    /**
     * @group Model
     */
    public function testReindexEverything(): void
    {
        self::$subject->setIndexerCode('html');

        try {
            self::assertInstanceOf(Subject::class, self::$subject->reindexEverything());
        } catch (Mage_Core_Exception $mageCoreException) {
            self::assertSame(self::INDEXER_MODEL_IS_NOT_DEFINED, $mageCoreException->getMessage());
        }
    }

    /**
     * @group Model
     */
    public function testDisableIndexerKeys(): void
    {
        self::$subject->setIndexerCode('html');

        try {
            self::assertInstanceOf(Subject::class, self::$subject->disableIndexerKeys());
        } catch (Mage_Core_Exception $mageCoreException) {
            self::assertSame(self::INDEXER_MODEL_IS_NOT_DEFINED, $mageCoreException->getMessage());
        }

    }

    /**
     * @group Model
     */
    public function testEnableIndexerKeys(): void
    {
        self::$subject->setIndexerCode('html');

        try {
            self::assertInstanceOf(Subject::class, self::$subject->enableIndexerKeys());
        } catch (Mage_Core_Exception $mageCoreException) {
            self::assertSame(self::INDEXER_MODEL_IS_NOT_DEFINED, $mageCoreException->getMessage());
        }
    }

    /**
     * @group Model
     */
    public function testGetUnprocessedEventsCollection(): void
    {
        self::$subject->setIndexerCode('html');
        self::assertInstanceOf(Mage_Index_Model_Resource_Event_Collection::class, self::$subject->getUnprocessedEventsCollection());
    }
}
