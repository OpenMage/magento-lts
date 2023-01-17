<?php

declare(strict_types=1);

/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Log
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Log data helper
 *
 * @category   Mage
 * @package    Mage_Log
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Log_Helper_Console extends Mage_Core_Helper_Abstract
{
    /**
     * Convert count to human view
     *
     * @param int $number
     * @return string
     */
    public function humanCount(int $number): string
    {
        if ($number < 1000) {
            return (string)$number;
        }
        if ($number < 1000000) {
            return sprintf('%.2fK', $number / 1000);
        }

        if ($number < 1000000000) {
            return sprintf('%.2fM', $number / 1000000);
        }

        return sprintf('%.2fB', $number / 1000000000);
    }

    /**
     * Convert size to human view
     *
     * @param int $number
     * @return string
     */
    public function humanSize(int $number): string
    {
        if ($number < 1000) {
            return sprintf('%d b', $number);
        }

        if ($number < 1000000) {
            return sprintf('%.2fKb', $number / 1000);
        }

        if ($number < 1000000000) {
            return sprintf('%.2fMb', $number / 1000000);
        }

        return sprintf('%.2fGb', $number / 1000000000);
    }
}
