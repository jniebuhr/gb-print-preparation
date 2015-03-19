<?php
namespace Goldbek\Cards\PrintPreparation\Model;

/**
 * Class Job
 * @package Goldbek\Cards\PrintPreparation\Model
 */
class Job {

    /**
     * @var string
     */
    protected $mode;
    /**
     * @var Size
     */
    protected $size;
    /**
     * @var Sheet
     */
    protected $sheet;
    /**
     * @var integer
     */
    protected $border;
    /**
     * @var string
     */
    protected $outfile;
    /**
     * @var Card[]
     */
    protected $cards;

    /**
     * @return int
     */
    public function getBorder()
    {
        return $this->border;
    }

    /**
     * @param int $border
     */
    public function setBorder($border)
    {
        $this->border = $border;
    }

    /**
     * @return Card[]
     */
    public function getCards()
    {
        return $this->cards;
    }

    /**
     * @param Card[] $cards
     */
    public function setCards($cards)
    {
        $this->cards = $cards;
    }

    /**
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * @param string $mode
     */
    public function setMode($mode)
    {
        $this->mode = $mode;
    }

    /**
     * @return string
     */
    public function getOutfile()
    {
        return $this->outfile;
    }

    /**
     * @param string $outfile
     */
    public function setOutfile($outfile)
    {
        $this->outfile = $outfile;
    }

    /**
     * @return Sheet
     */
    public function getSheet()
    {
        return $this->sheet;
    }

    /**
     * @param Sheet $sheet
     */
    public function setSheet($sheet)
    {
        $this->sheet = $sheet;
    }

    /**
     * @return Size
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param Size $size
     */
    public function setSize($size)
    {
        $this->size = $size;
    }
}
