<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Block\Newsletter\Problem\Grid\Filter;

use Mage_Adminhtml_Block_Newsletter_Problem_Grid_Filter_Checkbox as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Adminhtml\Block\Newsletter\Problem\Grid\Filter\CheckboxTrait;

final class CheckboxTest extends OpenMageTest
{
    use CheckboxTrait;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::markTestSkipped('');
    }
}
