<?php

/**
 * @author Alex10336
 * Dernière modification: $Id$
 * @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
 * @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
 *
 * */
class img {

    private $im;
    public $font;
    private $color, $w, $h, $tc;
    protected $colors = array();

    public function ComRadar($Coord, $level) {
        list($CoordsX, $CoordsY) = map::ss2xy($Coord);
        $x = 1 + ($CoordsY - 1) * $this->tc + round($this->tc / 2);
        $y = 1 + ($CoordsX - 1) * $this->tc + round($this->tc / 2);
        $ray = (($level) * 20) * $this->tc;
        ImageFilledEllipse($this->im, $x, $y, $ray, $ray, $this->color);
        return $this;
    }

    public function Plot($Coord) {
        list($CoordsX, $CoordsY) = map::ss2xy($Coord);
        $p1 = (($CoordsX - 1) * $this->tc) + 1;
        $p2 = ($CoordsY * $this->tc) + 1;
        $p3 = ($p1 + $this->tc) - 2;
        $p4 = ($p2 + $this->tc) - 2;

        ImageFilledRectangle($this->im, $p1, $p2, $p3, $p4, $this->color);
        return $this;
    }

    public function Dot($Coord) {
        $td = floor(($this->tc / 2));
        $td = ($td % 2) ? $td + 3 : $td + 2;

        list($sX, $sY) = map::ss2xy($Coord);
        $x1 = floor(($this->tc * $sX) - $this->tc / 2);
        $y1 = floor(($this->tc * ($sY + 1)) - $this->tc / 2);
        imagefilledellipse($this->im, $x1, $y1, $td, $td, $this->color);
        return $this;
    }

    public function path($CoordA, $CoordB) {
        list($sX, $sY) = map::ss2xy($CoordA);
        list($sX2, $sY2) = map::ss2xy($CoordB);
        $x1 = floor(($this->tc * $sX) - $this->tc / 2);
        $y1 = floor(($this->tc * ($sY + 1)) - $this->tc / 2);
        $x2 = floor(($this->tc * $sX2) - $this->tc / 2);
        $y2 = floor(($this->tc * ($sY2 + 1)) - $this->tc / 2);
        imageline($this->im, $x1, $y1, $x2, $y2, $this->color);
    }

    /**
     *
     * @param <type> $string
     * @param <type> $size
     * @return array
     */
    public function CenteredAxes($string, $size=10) {

        $axes = imageftbbox($size, 0, $this->font, $string);
        $x = ($this->w / 2) - (($axes[2] - $axes[0]) / 2);
        $y = ($this->h / 2) - (($axes[7] - $axes[1]) / 2);
        return array($x, $y);
    }

    /**
     *
     * @param <type> $string
     * @param <type> $cl
     * @param <type> $size
     * @return img
     */
    public function CenteredText($string, $size=10) {

        $axes = imageftbbox($size, 0, $this->font, $string);
        $x = ($this->w / 2) - (($axes[2] - $axes[0]) / 2);
        $y = ($this->h / 2) - (($axes[7] - $axes[1]) / 2);

        imagefttext($this->im, $size, 0, $x, $y, $this->color, $this->font, $string);
        return $this;
    }

    /**
     *
     * @param <type> $string
     * @param <type> $x
     * @param <type> $y
     * @param <type> $size
     * @return img
     */
    public function Text($string, $x, $y, $size=10) {
        imagefttext($this->im, $size, 0, $x, $y, $this->color, $this->font, $string);
        return $this;
    }

    /**
     *
     * @param <type> $hexa
     * @return img 
     */
    public function SetColorHexa($hexa) {
        if (!isset($this->colors[$hexa]))
            if (preg_match('/\#?([a-fA-F0-9]{2})([a-fA-F0-9]{2})([a-fA-F0-9]{2})/', $hexa, $matches))
                $this->colors[$hexa] = imagecolorallocate($this->im, hexdec($matches[1]), hexdec($matches[2]), hexdec($matches[3]));
        $this->color = $this->colors[$hexa];
        return $this;
    }

    /**
     *
     * @param <type> $r
     * @param <type> $g
     * @param <type> $b
     * @return img
     */
    public function SetColor($r, $g, $b) {
        $this->color = imagecolorallocate($this->im, $r, $g, $b);
        return $this;
    }

    /**
     * @param integer $r
     * @param integer $g
     * @param integer $b
     * @return img
     */
    public function FillAlpha($r=1, $g=1, $b=1) {
        $no_cl = imagecolorallocate($this->im, $r, $g, $b);
        imagecolortransparent($this->im, $no_cl);

        imagefilledrectangle($this->im, 0, 0, $this->w, $this->h, $no_cl);
        return $this;
    }

    public function FillAlphaHexa($hexa) {
        if (preg_match('/\#?([a-fA-F0-9]{2})([a-fA-F0-9]{2})([a-fA-F0-9]{2})/', $hexa, $matches)) {
            if (!isset($this->colors[$hexa]))
                $this->colors[$hexa] = imagecolorallocate($this->im, hexdec($matches[1]), hexdec($matches[2]), hexdec($matches[3]));
            imagecolortransparent($this->im, $this->colors[$hexa]);
            imagefilledrectangle($this->im, 0, 0, $this->w, $this->h, $this->colors[$hexa]);
        }
        return $this;
    }

    /**
     * @param integer $r
     * @param integer $g
     * @param integer $b
     * @return img
     */
    public function Fill($x=0, $y=0) {
        imagefilledrectangle($this->im, $x, $y, $this->w, $this->h, $this->color);
        return $this;
    }

    public function FillRectangle($x, $y, $w, $h) {
        imagefilledrectangle($this->im, $x, $y, $w, $h, $this->color);
        return $this;
    }

    public function SaveAs($filename) {
        imagepng($this->im, $filename);
        return $this;
    }

    public function Render() {
        header('Content-type: image/png');
        imagepng($this->im);
        exit(0);
    }

    public function RessourceID() {
        return $this->im;
    }

    public function __construct($width, $height) {
        $this->font = false;
        $this->color = 0;
        $this->im = imagecreatetruecolor($width, $height);
    }

    public function __destruct() {
        imagedestroy($this->im);
    }

    /**
     * @param integer $width
     * @param integer $height
     * @return img
     */
    static public function Create($width, $height) {
        $img = new img($width, $height);
        $img->w = $width;
        $img->h = $height;
        $img->tc = $img->w / 100;
        return $img;
    }

}