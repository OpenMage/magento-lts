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
 * @package     Mage_Install
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
 
/**
 * Administrator account install block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Install_Block_Admin extends Mage_Install_Block_Abstract
{
    public function __construct() 
    {
        parent::__construct();
        $this->setTemplate('install/create_admin.phtml');
    }
    
    public function getPostUrl()
    {
        return $this->getUrl('*/*/administratorPost');
    }
    
    public function getFormData()
    {
        $data = $this->getData('form_data');
        if (is_null($data)) {
            $data = new Varien_Object(Mage::getSingleton('install/session')->getAdminData(true));
            $this->setData('form_data', $data);
        }
        return $data;
    }
}
