<?php
namespace Goldbek\Cards\PrintPreparation\Model;

/**
 * Class Card
 * @package Goldbek\Cards\PrintPreparation\Model
 */
class Card {

    /**
     * @var string
     */
    protected $front;
    /**
     * @var string
     */
    protected $back;
    /**
     * @var Barcode
     */
    protected $barcode;

    /**
     * @return string
     */
    public function getBack()
    {
        return $this->back;
    }

    /**
     * @param string $back
     */
    public function setBack($back)
    {
        $this->back = $back;
    }

    /**
     * @return Barcode
     */
    public function getBarcode()
    {
        return $this->barcode;
    }

    /**
     * @param Barcode $barcode
     */
    public function setBarcode($barcode)
    {
        $this->barcode = $barcode;
    }

    /**
     * @return string
     */
    public function getFront()
    {
        return $this->front;
    }

    /**
     * @param string $front
     */
    public function setFront($front)
    {
        $this->front = $front;
    }
}
