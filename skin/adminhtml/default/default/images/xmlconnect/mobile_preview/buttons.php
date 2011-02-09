<?php

$imgPath = dirname(__FILE__).'//sort_buttons/buttons.gif';

$color = (isset($_GET['color']) && strlen($_GET['color'])==6) ? $_GET['color'] : 'ffffff';

$color = array(hexdec($color[0].$color[1]), hexdec($color[2].$color[3]), hexdec($color[4].$color[5]));


header ("Content-type: image/gif");

$im = imagecreatefromgif($imgPath);
imagealphablending($im, false);
    imagesavealpha($im, true);
$clr = imagecolorallocate($im, $color[0], $color[1], $color[2]);

$coordinates = array(
    array(4,  1,  69,  23),
    array(1,  2,  1,  23),
    array(2,  1,  2,  23),
    array(3,  1,  3,  23)
);
foreach ($coordinates as $rectangle) {
    imagefilledrectangle($im, $rectangle[0], $rectangle[1], $rectangle[2], $rectangle[3], $clr);
}
imagepng($im);
imagedestroy($im);
