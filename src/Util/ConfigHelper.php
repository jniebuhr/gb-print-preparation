<?php
namespace Goldbek\Cards\PrintPreparation\Util;

use Goldbek\Cards\PrintPreparation\Model\Barcode;
use Goldbek\Cards\PrintPreparation\Model\Card;
use Goldbek\Cards\PrintPreparation\Model\Job;
use Goldbek\Cards\PrintPreparation\Model\Sheet;
use Goldbek\Cards\PrintPreparation\Model\Size;

/**
 * Class ConfigHelper
 * The ConfigHelper class offers some methods to load json config files onto models.
 * @package Goldbek\Cards\PrintPreparation\Util
 */
class ConfigHelper {

    /**
     * Loads a Job model out of a given filename.
     * @param string $filename The filename to load
     * @return Job
     * @throws \Exception if the file can not be found or is not a json file.
     */
    public static function loadJob($filename)
    {
        $obj = self::loadObject($filename);
        $job = new Job();
        foreach (get_object_vars($obj) as $k => $v) {
            if ($k == 'mode') {
                $job->setMode($v);
            } else if ($k == 'border') {
                $job->setBorder($v);
            } else if ($k == 'outfile') {
                $job->setOutfile($v);
            } else if ($k == 'size') {
                $size = new Size();
                $size->setCols($v->cols);
                $size->setRows($v->rows);
                $size->setRotation($v->rotation);
                $size->setHeight($v->height);
                $size->setWidth($v->width);
                $job->setSize($size);
            } else if ($k == 'sheet') {
                $sheet = new Sheet();
                $sheet->setWidth($v->width);
                $sheet->setHeight($v->height);
                $job->setSheet($sheet);
            } else if ($k == 'cards') {
                $cardObjects = array();
                $cards = array();
                if (is_object($v)) {
                    $cards = array($v);
                } else if (is_array($v)) {
                    $cards = $v;
                } else {
                    throw new \Exception("No cards were configured in the given config.");
                }
                foreach ($cards as $c) {
                    $card = new Card();
                    $card->setFront($c->front);
                    $card->setBack($c->back);
                    if (isset($c->barcode)) {
                        $bc = new Barcode();
                        $bc->setX($c->barcode->x);
                        $bc->setY($c->barcode->y);
                        $bc->setWidth($c->barcode->width);
                        $bc->setHeight($c->barcode->height);
                        $bc->setValue($c->barcode->value);
                        $card->setBarcode($bc);
                    }
                    $cardObjects[] = $card;
                }
                $job->setCards($cardObjects);
            }
        }
        return $job;
    }

    /**
     * Loads an object out of a given filename.
     * @param $filename The filename to load
     * @return mixed
     * @throws \Exception if the file can not be found or is not a json file.
     */
    protected static function loadObject($filename)
    {
        if (!file_exists($filename)) {
            throw new \Exception("The file ". $filename ." could not be found.");
        }
        if (!is_readable($filename)) {
            throw new \Exception("The file ". $filename ." could not be read.");
        }
        $content = file_get_contents($filename);
        if (empty($content)) {
            throw new \Exception("The file was empty.");
        }
        $obj = json_decode($content);
        if (json_last_error() != JSON_ERROR_NONE) {
            throw new \Exception("The file did not contain correct json.");
        }
        return $obj;
    }
}
