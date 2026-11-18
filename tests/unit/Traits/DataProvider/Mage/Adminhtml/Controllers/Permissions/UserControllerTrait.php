<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Adminhtml\Controllers\Permissions;

use Generator;

trait UserControllerTrait
{
    /**
     * Loaded user, session user, posted data, data to save, whether the save is refused.
     * The loaded user has role 3.
     *
     * @return Generator<string, array<mixed>>
     */
    public static function providePrepareUserData(): Generator
    {
        yield 'another user keeps the posted status' => [7, 5, ['user_id' => '7', 'is_active' => '0'], ['user_id' => 7, 'is_active' => '0'], false];
        yield 'own account drops the posted status' => [7, 7, ['user_id' => '7', 'is_active' => '1'], ['user_id' => 7], false];
        yield 'own account with its current role' => [7, 7, ['roles' => ['3']], ['roles' => ['3'], 'user_id' => 7], false];
        yield 'own account with another role is refused' => [7, 7, ['roles' => ['4']], ['roles' => ['4'], 'user_id' => 7], true];
        yield 'posted own id does not replace the loaded user' => [9, 7, ['user_id' => '7', 'is_active' => '1'], ['user_id' => 9, 'is_active' => '1'], false];
        yield 'new user drops a posted id' => [null, 7, ['user_id' => '7', 'is_active' => '1'], ['is_active' => '1'], false];
    }
}
