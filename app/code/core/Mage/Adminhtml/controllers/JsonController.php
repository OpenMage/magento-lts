<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Json controller
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_JsonController extends Mage_Adminhtml_Controller_Action
{
    public const ADMIN_RESOURCE = true;

    /**
     * Return JSON-encoded array of country regions
     * @return void
     */
    public function countryRegionAction()
    {
        $arrRes = [];

        $countryId = $this->getRequest()->getParam('parent');
        $arrRegions = Mage::getResourceModel('directory/region_collection')
            ->addCountryFilter($countryId)
            ->load()
            ->toOptionArray();

        if ($arrRegions !== []) {
            foreach ($arrRegions as $region) {
                $arrRes[] = $region;
            }
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($arrRes));
    }
}
