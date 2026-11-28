<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Console
 */

declare(strict_types=1);

namespace OpenMage\Console\Command\Config;

use InvalidArgumentException;
use Mage;
use Mage_Core_Exception;
use Mage_Core_Model_Abstract;
use Mage_Core_Helper_Data;
use Mage_Core_Model_App_Area;
use OpenMage\Console\Command\AbstractCommand;

/**
 * Config commands ported from n98-magerun (https://github.com/netz98/n98-magerun), with the same names, scopes and options
 *
 * n98-magerun: Copyright (c) netz98 GmbH, MIT license, see lib/OpenMage/Console/LICENSE-n98-magerun.txt
 */
abstract class AbstractConfigCommand extends AbstractCommand
{
    public const SCOPES = ['default', 'websites', 'stores'];

    public const DISPLAY_NULL_VALUE = 'NULL (NULL/"unknown" value)';

    protected ?string $eventArea = Mage_Core_Model_App_Area::AREA_ADMINHTML;

    protected function checkScope(mixed $scope): string
    {
        if (!is_string($scope) || !in_array($scope, self::SCOPES, true)) {
            throw new InvalidArgumentException(sprintf(
                'Invalid scope "%s", expected one of: %s.',
                is_scalar($scope) ? (string) $scope : get_debug_type($scope),
                implode(', ', self::SCOPES),
            ));
        }

        return $scope;
    }

    /**
     * Converts a website or store code to its ID and checks that the website or store exists
     *
     * Without $force, the admin website and store (ID 0) are refused, given by ID or by code.
     */
    protected function resolveScopeId(string $scope, string $scopeId, bool $force = false): int
    {
        if ($scope === 'default') {
            if ($scopeId !== '0') {
                throw new InvalidArgumentException(sprintf('Invalid scope ID "%s" in scope default, must be 0.', $scopeId));
            }

            return 0;
        }

        $isId = preg_match('/^\d+$/', $scopeId) === 1;
        if ($isId && $force) {
            return (int) $scopeId;
        }

        if ($isId && (int) $scopeId === 0) {
            throw new InvalidArgumentException(sprintf(
                'Scope ID 0 in scope %s is the admin %s, use --force to write it anyway.',
                $scope,
                $scope === 'websites' ? 'website' : 'store',
            ));
        }

        $lookup = $isId ? (int) $scopeId : $scopeId;
        try {
            $entity = $scope === 'websites' ? Mage::app()->getWebsite($lookup) : Mage::app()->getStore($lookup);
        } catch (Mage_Core_Exception) {
            $entity = null;
        }

        if (!$entity instanceof Mage_Core_Model_Abstract) {
            throw new InvalidArgumentException(sprintf('Unknown %s "%s" in scope %s.', $isId ? 'ID' : 'code', $scopeId, $scope));
        }

        $entityId = (int) $entity->getId();
        if ($entityId === 0 && !$force) {
            $type = $scope === 'websites' ? 'website' : 'store';
            throw new InvalidArgumentException(sprintf(
                '%s "%s" in scope %s is the admin %s (ID 0), use --force to write it anyway.',
                ucfirst($type),
                $scopeId,
                $scope,
                $type,
            ));
        }

        return $entityId;
    }

    protected function coreHelper(): Mage_Core_Helper_Data
    {
        /** @var Mage_Core_Helper_Data $helper */
        $helper = Mage::helper('core');

        return $helper;
    }
}
