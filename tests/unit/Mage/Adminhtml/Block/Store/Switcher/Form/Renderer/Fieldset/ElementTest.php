<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Block\Store\Switcher\Form\Renderer\Fieldset;

use Mage_Adminhtml_Block_Store_Switcher_Form_Renderer_Fieldset_Element as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Adminhtml\Block\Store\Switcher\Form\Renderer\Fieldset\ElementTrait;

final class ElementTest extends OpenMageTest
{
    use ElementTrait;

    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = new Subject();
        self::markTestSkipped('');
    }
}
