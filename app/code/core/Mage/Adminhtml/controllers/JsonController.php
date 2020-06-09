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
 * @package     Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Json controller
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_JsonController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Return JSON-encoded array of country regions
     *
     * @return string
     */
    public function countryRegionAction()
    {
        $arrRes = array();

        $countryId = $this->getRequest()->getParam('parent');
        $arrRegions = Mage::getResourceModel('directory/region_collection')
            ->addCountryFilter($countryId)
            ->load()
            ->toOptionArray();

        if (!empty($arrRegions)) {
            foreach ($arrRegions as $region) {
                $arrRes[] = $region;
            }
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($arrRes));
    }

    /**
     * Check is allowed access to action
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return true;
    }
}
