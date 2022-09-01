<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Eav
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Eav
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * Properties:
 * - prefix
 * - pad_length
 * - pad_char
 * - last_id
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
        } else if (!empty($prefix = (string)$this->getPrefix()) && strpos($last, $prefix) === 0) {
            $last = (int)substr($last, strlen($prefix));
        } else {
            $last = (int)$last;
        }

        $next = $last+1;

        return $this->format($next);
    }
}
