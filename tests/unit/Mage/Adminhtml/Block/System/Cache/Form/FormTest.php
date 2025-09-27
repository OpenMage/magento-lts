<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Block\System\Cache\Form;

use Mage_Adminhtml_Block_System_Cache_Form as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

final class FormTest extends OpenMageTest
{
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = new Subject();
    }

    /**
     * @group Block
     */
    public function testInitForm(): void
    {
        self::assertInstanceOf(Subject::class, self::$subject->initForm());
    }
}
