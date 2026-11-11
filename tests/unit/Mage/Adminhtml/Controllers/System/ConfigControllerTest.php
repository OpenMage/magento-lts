<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Controllers\System;

use Mage;
use Mage_Adminhtml_System_ConfigController as Subject;
use Mage_Core_Model_Design_Package;
use Mage_Core_Controller_Request_Http;
use Mage_Core_Controller_Response_Http;
use OpenMage\Tests\Unit\OpenMageTest;
use PHPUnit\Framework\MockObject\MockObject;
use ReflectionProperty;

final class ConfigControllerTest extends OpenMageTest
{
    private const DESIGN_KEY = '_singleton/core/design_package';

    private ?int $storeId = null;

    private ?Mage_Core_Model_Design_Package $design = null;

    private string $layoutArea = '';

    /**
     * preDispatch() switches the current store, the design and the layout to the admin
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->storeId = Mage::app()->getStore()->getId();
        $this->design = clone Mage::getDesign();
        $this->layoutArea = (string) Mage::app()->getLayout()->getArea();
    }

    protected function tearDown(): void
    {
        Mage::app()->setCurrentStore($this->storeId);
        Mage::unregister(self::DESIGN_KEY);
        Mage::register(self::DESIGN_KEY, $this->design);
        Mage::app()->getLayout()->setArea($this->layoutArea);
        parent::tearDown();
    }

    /**
     * A role without system/config is forwarded to the denied action by the parent, so the
     * section check must not render the denied page a second time
     *
     * @group Controller
     */
    public function testPreDispatchSkipsSectionCheckWhenControllerAccessIsDenied(): void
    {
        $subject = $this->createSubject(false);
        $subject->expects(self::never())->method('_isSectionAllowed');

        $subject->preDispatch();

        self::assertTrue($subject->getFlag('', Subject::FLAG_NO_DISPATCH));
        self::assertSame('denied', $subject->getRequest()->getActionName());
    }

    /**
     * @group Controller
     */
    public function testPreDispatchChecksSectionWhenControllerAccessIsAllowed(): void
    {
        $subject = $this->createSubject(true);
        $subject->expects(self::once())->method('_isSectionAllowed')->with('general')->willReturn(true);

        $subject->preDispatch();

        self::assertFalse($subject->getFlag('', Subject::FLAG_NO_DISPATCH));
    }

    /**
     * @return MockObject&Subject
     */
    private function createSubject(bool $isAllowed): MockObject
    {
        $request = new Mage_Core_Controller_Request_Http();
        $request->setActionName('edit')->setParam('section', 'general')->setDispatched(true);

        $subject = $this->createPartialMock(Subject::class, ['_isAllowed', '_isSectionAllowed']);
        $subject->method('_isAllowed')->willReturn($isAllowed);
        (new ReflectionProperty($subject, '_request'))->setValue($subject, $request);
        (new ReflectionProperty($subject, '_response'))->setValue($subject, new Mage_Core_Controller_Response_Http());
        // Sessions and predispatch observers are not part of what is tested here
        $subject->setFlag('', Subject::FLAG_NO_START_SESSION, true);
        $subject->setFlag('', Subject::FLAG_NO_PRE_DISPATCH, true);

        return $subject;
    }
}
