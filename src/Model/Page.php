<?php
namespace Goldbek\Cards\PrintPreparation\Model;

/**
 * Class Page
 * @package Goldbek\Cards\PrintPreparation\Model
 */
class Page {

    /**
     * @var Element[]
     */
    protected $elements;

    /**
     * @return Element[]
     */
    public function getElements()
    {
        return $this->elements;
    }

    /**
     * @param Element[] $elements
     */
    public function setElements($elements)
    {
        $this->elements = $elements;
    }
}
