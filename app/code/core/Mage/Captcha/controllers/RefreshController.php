<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Captcha
 */

/**
 * Captcha controller
 *
 * @category   Mage
 * @package    Mage_Captcha
 */
class Mage_Captcha_RefreshController extends Mage_Core_Controller_Front_Action
{
    /**
     * Refreshes captcha and returns JSON encoded URL to image (AJAX action)
     * Example: {'imgSrc': 'http://example.com/media/captcha/67842gh187612ngf8s.png'}
     */
    public function indexAction()
    {
        $formId = $this->getRequest()->getPost('formId');
        $captchaModel = Mage::helper('captcha')->getCaptcha($formId);
        $this->getLayout()->createBlock($captchaModel->getBlockName())->setFormId($formId)->setIsAjax(true)->toHtml();
        $this->getResponse()->setBody(json_encode(['imgSrc' => $captchaModel->getImgSrc()]));
        $this->setFlag('', self::FLAG_NO_POST_DISPATCH, true);
    }
}
