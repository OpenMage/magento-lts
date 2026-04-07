<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Captcha\Model\Config\Form;

// use Mage;
// use Mage_Captcha_Model_Config_Form_Backend as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Captcha\Model\Config\Form\BackendTrait;

final class BackendTest extends OpenMageTest
{
    use BackendTrait;

    // private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = Mage::getModel('captcha/config_form_backend');
        self::markTestSkipped('');
    }
}
