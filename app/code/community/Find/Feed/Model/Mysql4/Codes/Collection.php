<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Find
 * @package     Find_Feed
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * TheFind feed codes (attribute map) collection
 *
 * @category    Find
 * @package     Find_Feed
 */
class Find_Feed_Model_Mysql4_Codes_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    /**
     * Local constructor
     *
     */
    protected function _construct()
    {
        $this->_init('find_feed/codes');
    }

    /**
     * Fetch attributes to import
     *
     * @return array
     */
    public function getImportAttributes() 
    {
        $this->addFieldToFilter('is_imported', array('eq' => '1'));
        return $this->_toOptionHash('import_code', 'eav_code');
    }

}
