<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Model\Resource\Email;

// use Mage;
// use Mage_Core_Model_Resource_Email_Template as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Core\Model\Resource\Email\TemplateTrait;

final class TemplateTest extends OpenMageTest
{
    use TemplateTrait;

    // private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = Mage::getModel('core/resource_email_template');
        self::markTestSkipped('');
    }
}
