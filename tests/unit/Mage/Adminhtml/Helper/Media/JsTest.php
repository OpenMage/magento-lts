<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Helper\Media;

use Mage;
use Mage_Adminhtml_Helper_Media_Js as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

final class JsTest extends OpenMageTest
{
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::helper('adminhtml/media_js');
    }

    /**
     * @group Helper
     */
    public function testDecodeGridSerializedInput(): void
    {
        self::assertIsString(self::$subject->getTranslatorScript());
    }
}
