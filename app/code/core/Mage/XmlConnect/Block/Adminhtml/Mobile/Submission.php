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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_XmlConnect_Block_Adminhtml_Mobile_Submission extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Class construct
     *
     * Setting buttons for submit application page
     */
    public function __construct()
    {
        $this->_objectId    = 'application_id';
        $this->_controller  = 'adminhtml_mobile';
        $this->_blockGroup  = 'xmlconnect';
        $this->_mode = 'submission';
        parent::__construct();

        $this->removeButton('delete');
        $this->removeButton('save');
        $this->removeButton('reset');

        $app = Mage::helper('xmlconnect')->getApplication();
        if ($app && $app->getIsResubmitAction()) {
            $label = $this->__('Resubmit App');
        } else {
            $label = $this->__('Submit App');
        }

        $this->_addButton('submission_post', array(
            'class' => 'save',
            'label' => $label,
            'onclick' => "submitApplication()",
        ));

        $this->_updateButton('back', 'label', $this->__('Back to App Edit'));
        $this->_updateButton('back', 'onclick', 'setLocation(\''. $this->getUrl('*/*/edit',
            array('application_id' => $app->getId())) . '\')');
    }

    /**
     * Get form header title
     * 
     * @return string
     */
    public function getHeaderText()
    {
        $app = Mage::helper('xmlconnect')->getApplication();
        if ($app && $app->getId()) {
            return $this->__('Submit App "%s"', $this->htmlEscape($app->getName()));
        }
        return '';
    }
}
