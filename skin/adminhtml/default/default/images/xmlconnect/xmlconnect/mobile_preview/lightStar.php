<?php 

$imgPath = dirname(__FILE__).'/lightStar.png';
$color = (isset($_GET['color']) && strlen($_GET['color'])==6) ? $_GET['color'] : 'ffffff';

$color = array(hexdec($color[0].$color[1]), hexdec($color[2].$color[3]), hexdec($color[4].$color[5]));

header ("Content-type: image/png");

$im = imagecreatefrompng($imgPath);
imagealphablending($im, false);
imagesavealpha($im, true);
$clr = imagecolorallocate($im, $color[0], $color[1], $color[2]);

$coordinates = array(
    array(0,  0,  4,  3 ),
    array(0,  4,  2,  4 ),
    array(5,  0,  5,  2 ),
    array(6,  0,  7,  0 ),
    array(9,  0,  13, 3 ),
    array(10, 4,  13, 4 ),
    array(8,  0,  8,  2 ),
    array(0,  7,  2,  12),
    array(0,  6,  1,  6 ),
    array(0,  5,  0,  5 ),
    array(11, 7,  13, 12),
    array(12, 6,  13, 6 ),
    array(13, 5,  13, 5 ),
    array(10, 12, 3,  12),
    array(5,  11, 7,  11),
    array(0,  8,  2,  12),
    array(11, 8,  13, 12),
    array(10, 8,  10, 12)  
);
foreach ($coordinates as $rectangle) {
    imagefilledrectangle($im, $rectangle[0], $rectangle[1], $rectangle[2], $rectangle[3], $clr);
}
imagepng($im);
imagedestroy($im);
