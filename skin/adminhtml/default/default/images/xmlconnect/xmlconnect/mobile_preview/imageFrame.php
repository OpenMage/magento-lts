<?php 

$imgPath = dirname(__FILE__).'/image-bg.png';
$color = (isset($_GET['color']) && strlen($_GET['color'])==6) ? $_GET['color'] : 'ff0000';
$color = array(hexdec($color[0].$color[1]), hexdec($color[2].$color[3]), hexdec($color[4].$color[5]));

header ("Content-type: image/png");

$im = imagecreatefrompng($imgPath);
imagealphablending($im, false);
        imagesavealpha($im, true);
$clr = imagecolorallocate($im, $color[0], $color[1], $color[2]);

$coordinates = array(
    array(0,  0,  81,  2 ),

    array(0,  3,  3,  3 ),
    array(0,  4,  0,  6 ),
    array(1,  4,  1,  4 ),

    array(0,  14, 0,  17),
    array(1,  17, 3,  17),
    array(1,  16, 1,  16),

    array(78, 3,  81,  3 ),
    array(81, 4,  81,  6 ),
    array(80, 4,  80,  4 ),

    array(78, 17, 81,  17),
    array(81, 17, 81,  14),
    array(80, 16, 80,  16)
);

foreach ($coordinates as $rectangle) {
    imagefilledrectangle($im, $rectangle[0], $rectangle[1], $rectangle[2], $rectangle[3], $clr);
}
imagepng($im);
imagedestroy($im);
