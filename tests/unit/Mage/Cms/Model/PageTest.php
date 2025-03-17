<?php

/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @group Mage_Cms
 * @group Mage_Cms_Model
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


    public function testLoad(): void
    {
        $this->assertInstanceOf(Subject::class, $this->subject->load(null));
        $this->assertInstanceOf(Subject::class, $this->subject->load(2));
    }


    public function testCheckIdentifier(): void
    {
        $this->assertIsString($this->subject->checkIdentifier('home', 1));
    }


    public function testGetCmsPageTitleByIdentifier(): void
    {
        if (defined('DATA_MAY_CHANGED')) {
            $this->markTestSkipped(self::SKIP_WITH_LOCAL_DATA);
        }
        $this->assertSame('Home page', $this->subject->getCmsPageTitleByIdentifier('home'));
    }


    public function testGetCmsPageTitleById(): void
    {
        if (defined('DATA_MAY_CHANGED')) {
            $this->markTestSkipped(self::SKIP_WITH_LOCAL_DATA);
        }
        $this->assertSame('Home page', $this->subject->getCmsPageTitleById(2));
    }


    public function testGetCmsPageIdentifierById(): void
    {
        $this->assertSame('home', $this->subject->getCmsPageIdentifierById(2));
    }


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


    public function testIsUsedInStoreConfig(): void
    {
        $this->assertFalse($this->subject->isUsedInStoreConfig());
    }
}
