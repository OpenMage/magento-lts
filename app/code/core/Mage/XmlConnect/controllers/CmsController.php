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
 * @package     Mage_XmlConnect
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * XmlConnect cms page controller
 *
 * @category    Mage
 * @package     Mage_Xmlconnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_CmsController extends Mage_XmlConnect_Controller_Action
{
    /**
     * Declare content type header
     *
     * @return null
     */
    public function preDispatch()
    {
        parent::preDispatch();
        $this->getResponse()->setHeader('Content-type', 'text/html; charset=UTF-8');
    }

    /**
     * Category list
     *
     * @return null
     */
    public function pageAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
    }

    /**
     * 3d secure authentication request page
     *
     * @return null
     */
    public function sentinelSecureAction()
    {
        try {
            $method = Mage::getSingleton('checkout/session')->getQuote()->getPayment()->getMethodInstance();
            if ($method->getIsCentinelValidationEnabled()) {
                $centinel = $method->getCentinelValidator();
                if ($centinel && $centinel->shouldAuthenticate()) {
                    /** @var $sentinelBlock Mage_Centinel_Block_Authentication */
                    $sentinelBlock = $this->getLayout()->addBlock('centinel/authentication', 'centinel.frame')
                        ->setTemplate('xmlconnect/centinel/authentication.phtml');
                    $this->getResponse()->setBody($sentinelBlock->toHtml());
                    return;
                }
            }
        } catch (Mage_Core_Exception $e) {
            $this->getResponse()->setBody($e->getMessage());
        } catch (Exception $e) {
            $this->getResponse()->setBody($e->getMessage());
            Mage::logException($e);
        }
    }
}
