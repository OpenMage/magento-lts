<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Block\Customer\Edit\Tab\View\Grid\Renderer;

use Mage_Adminhtml_Block_Customer_Edit_Tab_View_Grid_Renderer_Item as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Adminhtml\Block\Customer\Edit\Tab\View\Grid\Renderer\ItemTrait;

final class ItemTest extends OpenMageTest
{
    use ItemTrait;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::markTestSkipped('');
    }
}
