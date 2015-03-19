<?php
namespace Goldbek\Cards\PrintPreparation\Model;

/**
 * Class Sheet
 * @package Goldbek\Cards\PrintPreparation\Model
 */
class Sheet {

    /**
     * @var integer
     */
    protected $width;
    /**
     * @var integer
     */
    protected $height;

    /**
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param int $height
     */
    public function setHeight($height)
    {
        $this->height = $height;
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param int $width
     */
    public function setWidth($width)
    {
        $this->width = $width;
    }
}
