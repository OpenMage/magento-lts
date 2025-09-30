<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Catalog session model
 *
 * @package    Mage_Catalog
 *
 * @method $this setBeforeCompareUrl(string $value)
 * @method array getFormData()
 * @method $this setFormData(array $value)
 * @method int getLastViewedCategoryId()
 * @method int getLastViewedProductId()
 * @method $this setLastViewedProductId(int $value)
 * @method int getLastVisitedCategoryId()
 * @method string getLimitPage()
 * @method bool getParamsMemorizeDisabled()
 * @method array getSendfriendFormData()
 * @method $this setSendfriendFormData(array $value)
 * @method string getSortDirection()
 * @method string getSortOrder()
 * @method $this unsDisplayMode()
 * @method $this unsLimitPage()
 * @method $this unsSortDirection()
 * @method $this unsSortOrder()
 */
class Mage_Catalog_Model_Session extends Mage_Core_Model_Session_Abstract
{
    public function __construct()
    {
        $this->init('catalog');
    }

    /**
     * @return string
     */
    public function getDisplayMode()
    {
        return $this->_getData('display_mode');
    }
}
