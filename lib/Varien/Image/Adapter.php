<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Varien_Image
 */

class Varien_Image_Adapter
{
    public const ADAPTER_GD    = 'GD';
    public const ADAPTER_GD2   = 'GD2';
    public const ADAPTER_IM    = 'IMAGEMAGIC';

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

            default:
                throw new Exception('Invalid adapter selected.');
                break;
        }
    }
}
