<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\GoogleAnalytics\Block;

use Mage;
use Mage_Core_Controller_Request_Http;
use Mage_GoogleAnalytics_Block_Ga as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\GoogleAnalytics\Block\GaTrait;
use ReflectionMethod;

final class GaTest extends OpenMageTest
{
    use GaTrait;

    /**
     * A category page whose layout has no product_list_toolbar block must not break the page
     *
     * @group Block
     */
    public function testCategoryPageWithoutProductListToolbar(): void
    {
        self::assertFalse(Mage::app()->getLayout()->getBlock('product_list_toolbar'));

        $request = new Mage_Core_Controller_Request_Http();
        $request->setModuleName('catalog')->setControllerName('category')->setActionName('view');

        $subject = $this->createPartialMock(Subject::class, ['getRequest']);
        $subject->method('getRequest')->willReturn($request);

        $result = (new ReflectionMethod($subject, '_getEnhancedEcommerceDataForAnalytics4'))->invoke($subject);

        self::assertIsString($result);
        self::assertStringNotContainsString('view_item_list', $result);
    }
}
