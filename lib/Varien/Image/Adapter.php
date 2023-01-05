<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Varien
 * @package    Varien_Image
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2021-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Varien_Image_Adapter
{
    public const ADAPTER_GD    = 'GD';
    public const ADAPTER_GD2   = 'GD2';
    public const ADAPTER_IM    = 'IMAGEMAGIC';
    public const ADAPTER_IME   = 'IMAGEMAGIC_EXTERNAL';

    public static function factory($adapter)
    {
        switch ($adapter) {
            case self::ADAPTER_GD:
                return new Varien_Image_Adapter_Gd();
                break;

            case self::ADAPTER_GD2:
                return new Varien_Image_Adapter_Gd2();
                break;

            case self::ADAPTER_IM:
                return new Varien_Image_Adapter_Imagemagic();
                break;

            case self::ADAPTER_IME:
                return new Varien_Image_Adapter_ImagemagicExternal();
                break;

            default:
                throw new Exception('Invalid adapter selected.');
                break;
        }
    }
}
