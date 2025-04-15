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
use OpenMage\Tests\Unit\OpenMageTest;

class PageTest extends OpenMageTest
{
    public const SKIP_WITH_LOCAL_DATA = 'Constant DATA_MAY_CHANGED is defined.';

    private static Subject $subject;

    public function setUp(): void
    {
        self::$subject = Mage::getModel('cms/page');
    }

    /**
     * @group Mage_Cms
     * @group Mage_Cms_Model
     */
    public function testLoad(): void
    {
        static::assertInstanceOf(Subject::class, self::$subject->load(null));
        static::assertInstanceOf(Subject::class, self::$subject->load(2));
    }

    /**
     * @group Mage_Cms
     * @group Mage_Cms_Model
     */
    public function testCheckIdentifier(): void
    {
        static::assertIsString(self::$subject->checkIdentifier('home', 1));
    }

    /**
     * @group Mage_Cms
     * @group Mage_Cms_Model
     */
    public function testGetCmsPageTitleByIdentifier(): void
    {
        if (defined('DATA_MAY_CHANGED')) {
            static::markTestSkipped(self::SKIP_WITH_LOCAL_DATA);
        }
        static::assertSame('Home page', self::$subject->getCmsPageTitleByIdentifier('home'));
    }

    /**
     * @group Mage_Cms
     * @group Mage_Cms_Model
     */
    public function testGetCmsPageTitleById(): void
    {
        if (defined('DATA_MAY_CHANGED')) {
            static::markTestSkipped(self::SKIP_WITH_LOCAL_DATA);
        }
        static::assertSame('Home page', self::$subject->getCmsPageTitleById(2));
    }

    /**
     * @group Mage_Cms
     * @group Mage_Cms_Model
     */
    public function testGetCmsPageIdentifierById(): void
    {
        static::assertSame('home', self::$subject->getCmsPageIdentifierById(2));
    }

    /**
     * @group Mage_Cms
     * @group Mage_Cms_Model
     */
    public function testGetAvailableStatuses(): void
    {
        static::assertIsArray(self::$subject->getAvailableStatuses());
    }

    /**
     * @group Mage_Cms
     * @group Mage_Cms_Model
     * @doesNotPerformAssertions
     */
    public function testGetUsedInStoreConfigCollection(): void
    {
        self::$subject->getUsedInStoreConfigCollection();
    }

    /**
     * @group Mage_Cms
     * @group Mage_Cms_Model
     */
    public function testIsUsedInStoreConfig(): void
    {
        static::assertFalse(self::$subject->isUsedInStoreConfig());
    }
}
