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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_Catalog_Model_Design extends Mage_Core_Model_Abstract
{
    const APPLY_FOR_PRODUCT     = 1;
    const APPLY_FOR_CATEGORY    = 2;

    public function applyDesign($object, $calledFrom = 0)
    {
        $error      = 0;
        $package    = '';
        $theme      = '';
        $design     = $object->getCustomDesign();
        $date       = $object->getCustomDesignDate();
        $application= $object->getCustomDesignApply();

        $designInfo = explode("/", $design);
        if (count($designInfo) > 1){
            $package= $designInfo[0];
            $theme  = $designInfo[1];
        }

        switch ($calledFrom) {
            case self::APPLY_FOR_PRODUCT:
                $calledFrom = 3;
                break;

            case self::APPLY_FOR_CATEGORY:
                $calledFrom = 4;
                break;

            case 3:
                if ($application && $application != 1 && $application != 3)
                    $error = 1;

                $calledFrom = 0;
                break;

            case 4:
                if ($application && $application != 1 && $application != 4)
                    $error = 1;

                //$calledFrom = 0;
                break;

            default:
                if ($application && $application != 1)
                    $error = 1;

                break;
        }

        if ($package && $theme) {
            $date['from']   = strtotime($date['from']);
            $date['to']     = strtotime($date['to']);

            if ($date['from'] && $date['from'] > strtotime("today")) {
                $error = 1;
            } else if ($date['to'] && $date['to'] < strtotime("today")) {
                $error = 1;
            }

            if (!$error) {
                $this->_apply($package, $theme);
                return;
            }
        }

        if ($object instanceof Mage_Catalog_Model_Product) {
            $category = $object->getCategory();
        } else if ($object instanceof Mage_Catalog_Model_Category) {
            $category = $object->getParentCategory();
        }

        if ($category && $category->getId()){
            $this->applyDesign($category, $calledFrom);
        }
    }

    private function _apply($package, $theme)
    {
        Mage::getSingleton('core/design_package')
            ->setPackageName($package)
            ->setTheme($theme);
    }
}