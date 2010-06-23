<?php
/**
 * @author Alex10336
 * Dernière modification: $Id$
 * @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
 * @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
 *
 **/

class img {
    private $im;
    public $font;
    private $color;

    /**
     *
     * @param <type> $string
     * @param <type> $size
     * @return array
     */
    public function CenteredAxes($string, $size=10) {

        $axes = imageftbbox($size, 0, $this->font, $string);
        $x = (imagesx($this->im) / 2) - (($axes[2]-$axes[0])/2);
        $y = (imagesy($this->im) / 2) - (($axes[7]-$axes[1])/2);
        return array($x,$y);

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
        $x = (imagesx($this->im) / 2) - (($axes[2]-$axes[0])/2);
        $y = (imagesy($this->im) / 2) - (($axes[7]-$axes[1])/2);

        imagefttext($this->im, $size, 0, $x, $y, $this->color, $this->font, $string);
        return $this;

    }
    /**
     *
     * @param <type> $string
     * @param <type> $cl
     * @param <type> $x
     * @param <type> $y
     * @param <type> $size
     * @return img
     */
    public function Text($string, $cl, $x, $y, $size=10) {
        imagefttext($this->im, $size, 0, $x, $y, $this->color, $this->font, $string);
        return $this;
    }
    /**
     *
     * @param <type> $r
     * @param <type> $g
     * @param <type> $b
     * @return img 
     */
    public function SetColorHexa($r,$g,$b) {
        $this->color = imagecolorallocate ($this->im, hexdec($r), hexdec($g), hexdec($b));
        return $this;
    }
    /**
     *
     * @param <type> $r
     * @param <type> $g
     * @param <type> $b
     * @return img
     */
    public function SetColor($r,$g,$b) {
        $this->color = imagecolorallocate ($this->im, $r, $g, $b);
        return $this;
    }

    /**
     * @param integer $r
     * @param integer $g
     * @param integer $b
     * @return img
     */
    public function FillAlpha($r=1,$g=1,$b=1) {
        $no_cl = imagecolorallocate($this->im, $r,$g,$b);
        imagecolortransparent ($this->im, $no_cl);

        imagefilledrectangle($this->im, 0, 0, imagesx($this->im),  imagesy($this->im), $no_cl);
        return $this;
    }
    /**
     * @param integer $r
     * @param integer $g
     * @param integer $b
     * @return img
     */
    public function Fill($r,$g,$b) {
        $no_cl = imagecolorallocate($this->im, $r,$g,$b);
        imagefilledrectangle($this->im, 0, 0, imagesx($this->im),  imagesy($this->im), $no_cl);
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
    public function __construct($width, $height) {
        $this->font   = false;
        $this->color  = 0;
        $this->im     = imagecreatetruecolor($width, $height);
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
        return new img($width, $height);
    }
}