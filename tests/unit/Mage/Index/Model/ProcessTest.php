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
 * @copyright  Copyright (c) The OpenMage Contributors (https://openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Index\Model;

use Mage;
use Mage_Core_Exception;
use Mage_Index_Model_Process as Subject;
use Mage_Index_Model_Resource_Event_Collection;
use OpenMage\Tests\Unit\OpenMageTest;

class ProcessTest extends OpenMageTest
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
            static::assertInstanceOf(Subject::class, self::$subject->reindexEverything());
        } catch (Mage_Core_Exception $exception) {
            static::assertSame(self::INDEXER_MODEL_IS_NOT_DEFINED, $exception->getMessage());
        }
    }

    /**
     * @group Model
     */
    public function testDisableIndexerKeys(): void
    {
        self::$subject->setIndexerCode('html');

        try {
            static::assertInstanceOf(Subject::class, self::$subject->disableIndexerKeys());
        } catch (Mage_Core_Exception $exception) {
            static::assertSame(self::INDEXER_MODEL_IS_NOT_DEFINED, $exception->getMessage());
        }

    }

    /**
     * @group Model
     */
    public function testEnableIndexerKeys(): void
    {
        self::$subject->setIndexerCode('html');

        try {
            static::assertInstanceOf(Subject::class, self::$subject->enableIndexerKeys());
        } catch (Mage_Core_Exception $exception) {
            static::assertSame(self::INDEXER_MODEL_IS_NOT_DEFINED, $exception->getMessage());
        }
    }

    /**
     * @group Model
     */
    public function testGetUnprocessedEventsCollection(): void
    {
        self::$subject->setIndexerCode('html');
        static::assertInstanceOf(Mage_Index_Model_Resource_Event_Collection::class, self::$subject->getUnprocessedEventsCollection());
    }
}
