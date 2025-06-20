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
        return match ($adapter) {
            self::ADAPTER_GD => new Varien_Image_Adapter_Gd(),
            self::ADAPTER_GD2 => new Varien_Image_Adapter_Gd2(),
            self::ADAPTER_IM => new Varien_Image_Adapter_Imagemagic(),
            default => throw new Exception('Invalid adapter selected.'),
        };
    }
}
