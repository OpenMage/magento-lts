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
 * @copyright  Copyright (c) 2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Cms\Model;

use Mage;
use Mage_Cms_Model_Page as Subject;
use Mage_Core_Model_Resource_Db_Collection_Abstract;
use PHPUnit\Framework\TestCase;

class PageTest extends TestCase
{
    public const SKIP_WITH_LOCAL_DATA = 'Constant DATA_MAY_CHANGED is defined.';

    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::getModel('cms/page');
    }

    /**
     * @group Mage_Cms
     * @group Mage_Cms_Model
     */
    public function testLoad(): void
    {
        $this->assertInstanceOf(Subject::class, $this->subject->load(null));
        $this->assertInstanceOf(Subject::class, $this->subject->load(2));
    }

    /**
     * @group Mage_Cms
     * @group Mage_Cms_Model
     */
    public function testCheckIdentifier(): void
    {
        $this->assertIsString($this->subject->checkIdentifier('home', 1));
    }

    /**
     * @group Mage_Cms
     * @group Mage_Cms_Model
     */
    public function testGetCmsPageTitleByIdentifier(): void
    {
        if (defined('DATA_MAY_CHANGED')) {
            $this->markTestSkipped(self::SKIP_WITH_LOCAL_DATA);
        }
        $this->assertSame('Home page', $this->subject->getCmsPageTitleByIdentifier('home'));
    }

    /**
     * @group Mage_Cms
     * @group Mage_Cms_Model
     */
    public function testGetCmsPageTitleById(): void
    {
        if (defined('DATA_MAY_CHANGED')) {
            $this->markTestSkipped(self::SKIP_WITH_LOCAL_DATA);
        }
        $this->assertSame('Home page', $this->subject->getCmsPageTitleById(2));
    }

    /**
     * @group Mage_Cms
     * @group Mage_Cms_Model
     */
    public function testGetCmsPageIdentifierById(): void
    {
        $this->assertSame('home', $this->subject->getCmsPageIdentifierById(2));
    }

    /**
     * @group Mage_Cms
     * @group Mage_Cms_Model
     */
    public function testGetAvailableStatuses(): void
    {
        $this->assertIsArray($this->subject->getAvailableStatuses());
    }

    /**
     * @group Mage_Cms
     * @group Mage_Cms_Model
     * @doesNotPerformAssertions
     */
    public function testGetUsedInStoreConfigCollection(): void
    {
        $this->subject->getUsedInStoreConfigCollection();
    }

    /**
     * @group Mage_Cms
     * @group Mage_Cms_Model
     */
    public function testIsUsedInStoreConfig(): void
    {
        $this->assertFalse($this->subject->isUsedInStoreConfig());
    }
}
