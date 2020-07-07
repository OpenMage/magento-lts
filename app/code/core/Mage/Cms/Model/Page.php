<?php
/**
 * Magento
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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Cms
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Cms Page Model
 *
 * @method Mage_Cms_Model_Resource_Page _getResource()
 * @method Mage_Cms_Model_Resource_Page getResource()
 * @method Mage_Cms_Model_Resource_Page_Collection getCollection()
 *
 * @method string getContentHeading()
 * @method $this setContentHeading(string $value)
 * @method string getContent()
 * @method $this setContent(string $value)
 * @method string getCreationTime()
 * @method $this setCreationTime(string $value)
 * @method int getIsActive()
 * @method $this setIsActive(int $value)
 * @method string getLayoutUpdateXml()
 * @method $this setLayoutUpdateXml(string $value)
 * @method bool hasCreationTime()
 * @method string getCustomTheme()
 * @method $this setCustomTheme(string $value)
 * @method string getCustomRootTemplate()
 * @method $this setCustomRootTemplate(string $value)
 * @method string getCustomLayoutUpdateXml()
 * @method $this setCustomLayoutUpdateXml(string $value)
 * @method string getCustomThemeFrom()
 * @method $this setCustomThemeFrom(string $value)
 * @method string getCustomThemeTo()
 * @method $this setCustomThemeTo(string $value)
 * @method string getIdentifier()
 * @method $this setIdentifier(string $value)
 * @method string getMetaDescription()
 * @method $this setMetaDescription(string $value)
 * @method string getMetaKeywords()
 * @method $this setMetaKeywords(string $value)
 * @method string getPreviewUrl()
 * @method string getRootTemplate()
 * @method $this setRootTemplate(string $value)
 * @method $this setStoreId(int $value)
 * @method int getSortOrder()
 * @method $this setSortOrder(int $value)
 * @method bool hasStores()
 * @method array getStores()
 * @method string getStoreCode()
 * @method string getStoreId()
 * @method string getTitle()
 * @method $this setTitle(string $value)
 * @method string getUpdateTime()
 * @method $this setUpdateTime(string $value)
 *
 * @category    Mage
 * @package     Mage_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Cms_Model_Page extends Mage_Core_Model_Abstract
{
    const NOROUTE_PAGE_ID = 'no-route';

    /**
     * Page's Statuses
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    const CACHE_TAG              = 'cms_page';
    protected $_cacheTag         = 'cms_page';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'cms_page';

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('cms/page');
    }

    /**
     * @inheritDoc
     */
    public function load($id, $field = null)
    {
        if (is_null($id)) {
            return $this->noRoutePage();
        }
        return parent::load($id, $field);
    }

    /**
     * Load No-Route Page
     *
     * @return $this
     */
    public function noRoutePage()
    {
        return $this->load(self::NOROUTE_PAGE_ID, $this->getIdFieldName());
    }

    /**
     * Check if page identifier exist for specific store
     * return page id if page exists
     *
     * @param string $identifier
     * @param int $storeId
     * @return string
     */
    public function checkIdentifier($identifier, $storeId)
    {
        return $this->_getResource()->checkIdentifier($identifier, $storeId);
    }

    /**
     * Prepare page's statuses.
     * Available event cms_page_get_available_statuses to customize statuses.
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        $statuses = new Varien_Object(array(
            self::STATUS_ENABLED => Mage::helper('cms')->__('Enabled'),
            self::STATUS_DISABLED => Mage::helper('cms')->__('Disabled'),
        ));

        Mage::dispatchEvent('cms_page_get_available_statuses', array('statuses' => $statuses));

        return $statuses->getData();
    }
}
