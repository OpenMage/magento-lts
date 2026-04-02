<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Core\Model;

use Generator;
use Mage_Core_Model_Store;
use Mage_Core_Model_Store_Group;
use Mage_Core_Model_Website;

trait AppTrait
{
    public function provideGetStoreData(): Generator
    {
        yield 'Mage_Core_Model_Store' => [
            new Mage_Core_Model_Store(),
        ];
    }

    public function provideGetWebsiteData(): Generator
    {
        yield 'Mage_Core_Model_Website' => [
            new Mage_Core_Model_Website(),
        ];
    }

    public function provideGetGroupData(): Generator
    {
        yield 'Mage_Core_Model_Store_Group' => [
            new Mage_Core_Model_Store_Group(),
        ];
    }
}
