<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\CatalogRule\Model\Action\Index;

// use Mage;
// use Mage_CatalogRule_Model_Action_Index_Refresh as Subject;
use Override;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\CatalogRule\Model\Action\Index\RefreshTrait;

final class RefreshTest extends OpenMageTest
{
    use RefreshTrait;

    // private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = Mage::getModel('catalogrule/action_index_refresh');
        self::markTestSkipped('');
    }
}
