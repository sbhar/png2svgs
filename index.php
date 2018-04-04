<?php
// convert PNG image to SVG
$pixel_style = array(
    'fill' => '',
    'fill-opacity' => 1,
    'stroke' => '#d2d2d2',
    'stroke-linejoin' => 'round',
    'stroke-opacity' => 1,
);
$pixel_size = 10;
//$filename = $argv[1];
$filename = 'test.png';
if (! file_exists($filename) ) {
    die('No existe la imágen: ' . $filename);
}
// read image
$size = getimagesize($filename);
//print_r($size);

$width  = $size[0];
$height = $size[1];
//echo 'width: ' . $width . '<br/>height: ' . $height;

// open image into a true color image
$orimage = imagecreatefrompng($filename);

$im = imagecreatetruecolor($width,$height);
//header("Content-Type: image/png");
//echo $im;
//exit;
imagecopyresampled($im,$orimage,0,0,0,0, $width,$height,$width,$height);
// header
output_head();

// body
$svg = '';
$colors = array();
for($y = 0; $y < $height; $y++) {
    for($x = 0; $x < $width; $x++) {
        // get pixel at (x,y)
        $rgb = imagecolorat($im, $x, $y);
        $r = ($rgb >> 16) & 0xFF;
        $g = ($rgb >> 8) & 0xFF;
        $b = $rgb & 0xFF;
        // skip black pixels
        if ($rgb == 0) continue;
        $color = sprintf("#%02x%02x%02x", $r, $g, $b);
        // calculate color for border
        $dark_factor = 0.9;
        $border_color = sprintf(
            "#%02x%02x%02x", 
            (int) ($r * $dark_factor), 
            (int) ($g * $dark_factor), 
            (int) ($b * $dark_factor)
        );
        // calculamos el estilo del pixel
        $style = $pixel_style;
        $style['fill'] = $color;
        $style['stroke'] = $border_color;
        // guardamos el estilo para el color key
        if (!isset($colors[$rgb])) { 
            $colors[$rgb] = $style;
        }
        $svg .= '<rect height="10" width="10" x="' . $x * $pixel_size . '" y="' . $y * $pixel_size . '" style="' . to_svg_style($style) . '" />';
        $svg .= "\n";
    }
}
$y += 3 * $pixel_size;
$x = 0;
foreach($colors as $color => $style) {
    $svg .= '<rect height="10" width="10" x="' . 2 * $x * $pixel_size . '" y="' . $y * $pixel_size . '" style="' . to_svg_style($style) . '" />';
    $x++;
}
echo $svg;
// footer
output_foot();
exit(0);
function to_svg_style($s) {
    $l = array();
    foreach($s as $k => $v) {
        $l[] = "$k:$v";
    }
    return implode($l, ';');
}
function output_head() {
$svg = '<?xml version="1.0" encoding="utf-8"?>
<!-- Generator: Adobe Illustrator 16.0.2, SVG Export Plug-In . SVG Version: 6.00 Build 0)  -->
<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd"><svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	 width="144px" height="128px" viewBox="0 0 144 128" enable-background="new 0 0 144 128" xml:space="preserve">';
echo $svg;
}
function output_foot() {
    $svg = '  </svg>';
    echo $svg;
}
// vim: se ts=4 sw=4 ai expandtab: