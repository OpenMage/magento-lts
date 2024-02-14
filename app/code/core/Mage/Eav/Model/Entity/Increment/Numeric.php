<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Eav
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Eav
 */
class Mage_Eav_Model_Entity_Increment_Numeric extends Mage_Eav_Model_Entity_Increment_Abstract
{
    /**
     * @return string
     */
    public function getNextId()
    {
        $last = $this->getLastId();

        if (empty($last)) {
            $last = 0;
        } elseif (!empty($prefix = (string)$this->getPrefix()) && str_starts_with($last, $prefix)) {
            $last = (int)substr($last, strlen($prefix));
        } else {
            $last = (int)$last;
        }

        $next = $last + 1;

        return $this->format($next);
    }
}
