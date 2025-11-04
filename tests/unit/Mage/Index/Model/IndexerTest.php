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
use Mage_Index_Model_Event;
use Mage_Index_Model_Indexer as Subject;
use Mage_Index_Model_Resource_Process_Collection;
use OpenMage\Tests\Unit\OpenMageTest;

final class IndexerTest extends OpenMageTest
{
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('index/indexer');
    }

    /**
     * @covers Mage_Index_Model_Indexer::getProcessesCollection()
     * @group Model
     */
    public function testGetProcessesCollection(): void
    {
        self::assertInstanceOf(Mage_Index_Model_Resource_Process_Collection::class, self::$subject->getProcessesCollection());
    }

    /**
     * @covers Mage_Index_Model_Indexer::hasErrors()
     * @group Model
     */
    public function testHasErrors(): void
    {
        self::assertIsBool(self::$subject->hasErrors());
    }

    /**
     * @covers Mage_Index_Model_Indexer::getErrors()
     * @group Model
     */
    public function testGetErrors(): void
    {
        self::assertIsArray(self::$subject->getErrors());
    }

    /**
     * @covers Mage_Index_Model_Indexer::lockIndexer()
     * @group Model
     */
    public function testLockIndexer(): void
    {
        self::assertInstanceOf(Subject::class, self::$subject->lockIndexer());
    }

    /**
     * @covers Mage_Index_Model_Indexer::unlockIndexer()
     * @group Model
     */
    public function testUnlockIndexer(): void
    {
        self::assertInstanceOf(Subject::class, self::$subject->unlockIndexer());
    }

    /**
     * @group Model
     */
    public function testIndexEvent(): void
    {
        $event = new Mage_Index_Model_Event();
        self::assertInstanceOf(Subject::class, self::$subject->indexEvent($event));
    }

    /**
     * @group Model
     */
    public function testRegisterEvent(): void
    {
        $event = new Mage_Index_Model_Event();
        self::assertInstanceOf(Subject::class, self::$subject->registerEvent($event));
    }

    /**
     * @covers Mage_Index_Model_Indexer::allowTableChanges()
     * @group Model
     */
    public function testAllowTableChanges(): void
    {
        self::assertInstanceOf(Subject::class, self::$subject->allowTableChanges());
    }

    /**
     * @covers Mage_Index_Model_Indexer::disallowTableChanges()
     * @group Model
     */
    public function testDisallowTableChanges(): void
    {
        self::assertInstanceOf(Subject::class, self::$subject->disallowTableChanges());
    }
}
