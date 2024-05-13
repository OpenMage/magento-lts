<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Tests
 * @package    Tests_Functional
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Magento\Mtf\App\State;

/**
 * Abstract class AbstractState.
 */
abstract class AbstractState implements StateInterface
{
    /**
     * Specifies whether to clean instance under test.
     *
     * @var bool
     */
    protected $isCleanInstance = false;

    /**
     * Apply set up configuration profile.
     *
     * @return void
     */
    public function apply()
    {
        //
    }

    /**
     * Clear Magento instance: remove all tables in DB and use dump to load new ones, clear Magento cache.
     *
     * @return void
     */
    public function clearInstance()
    {
        //
    }
}
