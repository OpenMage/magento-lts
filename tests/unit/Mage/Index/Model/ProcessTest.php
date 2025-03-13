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
use PHPUnit\Framework\TestCase;

class ProcessTest extends TestCase
{
    public const INDEXER_MODEL_IS_NOT_DEFINED = 'Indexer model is not defined.';

    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::getModel('index/process');
    }

    /**
     * @group Mage_Index
     * @group Mage_Index_Model
     */
    public function testReindexEverything(): void
    {
        $this->subject->setIndexerCode('html');

        try {
            $this->assertInstanceOf(Subject::class, $this->subject->reindexEverything());
        } catch (Mage_Core_Exception $exception) {
            $this->assertSame(self::INDEXER_MODEL_IS_NOT_DEFINED, $exception->getMessage());
        }
    }

    /**
     * @group Mage_Index
     * @group Mage_Index_Model
     */
    public function testDisableIndexerKeys(): void
    {
        $this->subject->setIndexerCode('html');

        try {
            $this->assertInstanceOf(Subject::class, $this->subject->disableIndexerKeys());
        } catch (Mage_Core_Exception $exception) {
            $this->assertSame(self::INDEXER_MODEL_IS_NOT_DEFINED, $exception->getMessage());
        }

    }

    /**
     * @group Mage_Index
     * @group Mage_Index_Model
     */
    public function testEnableIndexerKeys(): void
    {
        $this->subject->setIndexerCode('html');

        try {
            $this->assertInstanceOf(Subject::class, $this->subject->enableIndexerKeys());
        } catch (Mage_Core_Exception $exception) {
            $this->assertSame(self::INDEXER_MODEL_IS_NOT_DEFINED, $exception->getMessage());
        }
    }

    /**
     * @group Mage_Index
     * @group Mage_Index_Model
     */
    public function testGetUnprocessedEventsCollection(): void
    {
        $this->subject->setIndexerCode('html');
        $this->assertInstanceOf(Mage_Index_Model_Resource_Event_Collection::class, $this->subject->getUnprocessedEventsCollection());
    }
}
