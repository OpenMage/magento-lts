<?php
/**
 * OpenMage
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
 * @category   Mage
 * @package    Mage_Captcha
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Captcha interface
 *
 * @category   Mage
 * @package    Mage_Captcha
 * @author     Magento Core Team <core@magentocommerce.com>
 */
interface Mage_Captcha_Model_Interface
{
    /**
     * Generates captcha
     *
     * @abstract
     */
    public function generate();

    /**
     * Checks whether word entered by user corresponds to the one generated by generate()
     *
     * @abstract
     * @param string $word
     * @return void
     */
    public function isCorrect($word);

 /**
     * Get Block Name
     *
     * @return string
     */
    public function getBlockName();
}
