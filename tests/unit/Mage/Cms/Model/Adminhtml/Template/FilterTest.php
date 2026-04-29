<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Cms\Model\Adminhtml\Template;

// use Mage;
// use Mage_Cms_Model_Adminhtml_Template_Filter as Subject;
use Override;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Cms\Model\Adminhtml\Template\FilterTrait;

final class FilterTest extends OpenMageTest
{
    use FilterTrait;

    // private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = Mage::getModel('cms/adminhtml_template_filter');
        self::markTestSkipped('');
    }
}
