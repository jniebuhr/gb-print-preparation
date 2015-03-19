<?php
namespace Goldbek\Cards\PrintPreparation\Service;

use CLIFramework\Logger;
use Goldbek\Cards\PrintPreparation\Model\Element;
use Goldbek\Cards\PrintPreparation\Model\Job;
use Goldbek\Cards\PrintPreparation\Model\Page;
use Goldbek\Cards\PrintPreparation\Model\Sheet;
use Goldbek\Cards\PrintPreparation\Model\Size;
use phpDocumentor\Plugin\Pdf\Writer\Pdf;
use ZendPdf\Color\Html;
use ZendPdf\Color\Rgb;
use ZendPdf\Image;
use ZendPdf\PdfDocument;

/**
 * Class GenerationService
 * This service handles the generation of an output pdf by supplying it with a job.
 * @package Goldbek\Cards\PrintPreparation\Service
 */
class GenerationService
{

    const CUTMARK_WIDTH = .25;
    const CUTMARK_HEIGHT = 5;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var array
     */
    protected $imageCache = array();

    /**
     * Default constructor for GenerationService
     */
    public function __construct()
    {

    }

    /**
     * Injects the logger into this service.
     * @param Logger $logger
     * @return void
     */
    public function injectLogger($logger)
    {
        $this->logger = $logger;
    }

    /**
     * Executes the GenerationService for the given job.
     * @param Job $job The job to execute.
     * @return boolean {@code true} if it succeeded.
     */
    public function execute(Job $job)
    {
        $this->logger->info('Executing Job.');
        $pages = $this->generatePages($job);
        $this->drawPdf($job, $pages);
    }

    /**
     * Draws the pages for the given job.
     * @param Job    $job   The job to execute.
     * @param Page[] $pages The pages to draw.
     */
    protected function drawPdf(Job $job, $pages)
    {
        $pdf = new PdfDocument();
        foreach ($pages as $p) {
            $page = $pdf->newPage($job->getSheet()->getWidth(), $job->getSheet()->getHeight());
            $pdf->pages[] = $page;
            foreach ($p->getElements() as $element) {
                if ($element->getType() == 'mark') {
                    $page->setLineWidth(self::CUTMARK_WIDTH);
                    $page->setLineColor(new Html($element->getValue()));
                    $x2 = $element->getX() + ($element->getWidth() == self::CUTMARK_WIDTH ? 0 : $element->getWidth());
                    $y2 = $element->getY() + ($element->getHeight() == self::CUTMARK_WIDTH ? 0 : $element->getHeight());
                    $page->drawLine($element->getX(), $element->getY(), $x2, $y2);
                } else if ($element->getType() == 'card') {
                    $image = null;
                    if ($element->getRotation() <> 0) {
                        $image = $this->loadRotatedImage($element->getValue(), $element->getRotation());
                    } else {
                        $image = $this->loadImage($element->getValue());
                    }
                    $page->drawImage(
                        $image,
                        $element->getX(),
                        $element->getY(),
                        $element->getX() + $element->getWidth(),
                        $element->getY() + $element->getHeight()
                    );
                }
            }
        }
        $pdf->save($job->getOutfile());
    }

    /**
     * Generates the pages for the given job.
     * @param Job $job The job to generate pages for.
     * @return Page[] The pages generated.
     */
    protected function generatePages(Job $job)
    {
        $pages = array();
        // This is the default implementation, only one card is used.
        $cards = $job->getCards();
        $card = $cards[0];
        // Generate front page
        $frontPage = new Page();
        $frontElements = $this->generateElements(
            $job->getSheet(),
            $job->getSize(),
            $job->getBorder(),
            $card->getFront()
        );
        $frontPage->setElements($frontElements);
        $pages[] = $frontPage;
        // Generate back page
        $backPage = new Page();
        $backElements = $this->generateElements(
            $job->getSheet(),
            $job->getSize(),
            $job->getBorder(),
            $card->getBack()
        );
        $backPage->setElements($backElements);
        $pages[] = $backPage;
        return $pages;
    }

    /**
     * Generates elements based on the Sheet size and layout.
     * @param Sheet $sheet The sheet to use.
     * @param Size $size The size layout to use.
     * @param integer $border The border to use.
     * @param string $cardFile The card file to use.
     * @return Element[]
     */
    protected function generateElements(Sheet $sheet, Size $size, $border, $cardFile)
    {
        $elements = array();
        $cardWidth = $size->getWidth() + ($border * 2);
        $cardHeight = $size->getHeight() + ($border * 2);
        $totalCardWidth = $cardWidth * $size->getCols();
        $totalCardHeight = $cardHeight * $size->getRows();
        if ($totalCardWidth > $sheet->getWidth() || $totalCardHeight > $sheet->getHeight()) {
            throw new \Exception("The supplied layout does not fit the sheet.");
        }
        $cardStartX = ($sheet->getWidth() / 2) - ($totalCardWidth / 2);
        $cardStartY = ($sheet->getHeight() / 2) - ($totalCardHeight / 2);
        for ($x = 0; $x < $size->getCols(); $x++) {
            for ($y = 0; $y < $size->getRows(); $y++) {
                $element = new Element();
                $element->setWidth($cardWidth);
                $element->setHeight($cardHeight);
                $element->setRotation($size->getRotation());
                $cardX = $cardStartX + $cardWidth * $x;
                $cardY = $cardStartY + $cardHeight * $y;
                $element->setX($cardX);
                $element->setY($cardY);
                $element->setType('card');
                $element->setValue($cardFile);
                $this->logger->info2("Placing card at x=".$cardX.",y=".$cardY." with value ".$cardFile."");
                $elements[] = $element;
                // Check if we need to generate cut marks for this card.
                if ($x == 0) { // Generate left cutmarks
                    $marks = $this->generateMarks('left', $cardX, $cardY, $cardWidth, $cardHeight, $border);
                    $elements = array_merge($elements, $marks);
                }
                if ($y == 0) { // Generate top cutmarks
                    $marks = $this->generateMarks('top', $cardX, $cardY, $cardWidth, $cardHeight, $border);
                    $elements = array_merge($elements, $marks);
                }
                if ($x == $size->getCols() - 1) { // Generate right cutmarks
                    $marks = $this->generateMarks('right', $cardX, $cardY, $cardWidth, $cardHeight, $border);
                    $elements = array_merge($elements, $marks);
                }
                if ($y == $size->getRows() - 1) { // Generate bottom cutmarks
                    $marks = $this->generateMarks('bottom', $cardX, $cardY, $cardWidth, $cardHeight, $border);
                    $elements = array_merge($elements, $marks);
                }
            }
        }
        return $elements;
    }

    /**
     * Generates marks based on card position and orientation.
     * @param string $orientation The orientation to draw the marks (left|right|bottom|top)
     * @param integer $cardX The cards X coordinate
     * @param integer $cardY The cards Y coordinate
     * @param integer $cardWidth The cards width
     * @param integer $cardHeight The cards height
     * @param integer $border The border to use
     * @return Element[]
     */
    protected function generateMarks($orientation, $cardX, $cardY, $cardWidth, $cardHeight, $border)
    {
        $marks = array();
        if ($orientation == 'left' || $orientation == 'right') {
            $x = $orientation == 'left' ? $cardX - self::CUTMARK_HEIGHT : $cardX + $cardWidth;
            $markA = new Element();
            $markA->setRotation(0);
            $markA->setWidth(self::CUTMARK_HEIGHT);
            $markA->setHeight(self::CUTMARK_WIDTH);
            $markA->setX($x);
            $markA->setY($cardY + $border);
            $markA->setType('mark');
            $markA->setValue('#000000');
            $this->logger->info2("Placing ".$orientation." cut mark at x=".$markA->getX().",y=".$markA->getY());
            $markB = new Element();
            $markB->setRotation(0);
            $markB->setWidth(self::CUTMARK_HEIGHT);
            $markB->setHeight(self::CUTMARK_WIDTH);
            $markB->setX($x);
            $markB->setY($cardY + $cardHeight - $border);
            $markB->setType('mark');
            $markB->setValue('#000000');
            $this->logger->info2("Placing ".$orientation." cut mark at x=".$markB->getX().",y=".$markB->getY());
            $marks[] = $markA;
            $marks[] = $markB;
        } else if ($orientation == 'top' || $orientation == 'bottom') {
            $y = $orientation == 'top' ? $cardY - self::CUTMARK_HEIGHT : $cardY + $cardHeight;
            $markA = new Element();
            $markA->setRotation(0);
            $markA->setWidth(self::CUTMARK_WIDTH);
            $markA->setHeight(self::CUTMARK_HEIGHT);
            $markA->setX($cardX + $border);
            $markA->setY($y);
            $markA->setType('mark');
            $markA->setValue('#000000');
            $this->logger->info2("Placing ".$orientation." cut mark at x=".$markA->getX().",y=".$markA->getY());
            $markB = new Element();
            $markB->setRotation(0);
            $markB->setWidth(self::CUTMARK_WIDTH);
            $markB->setHeight(self::CUTMARK_HEIGHT);
            $markB->setX($cardX + $cardWidth - $border);
            $markB->setY($y);
            $markB->setType('mark');
            $markB->setValue('#000000');
            $this->logger->info2("Placing ".$orientation." cut mark at x=".$markB->getX().",y=".$markB->getY());
            $marks[] = $markA;
            $marks[] = $markB;
        }
        return $marks;
    }

    /**
     * Loads an image or uses a cached version of that image.
     * @param string  $filename The filename to load
     * @param boolean $unlink   If the file should be unlinked after loading
     * @return \ZendPdf\Resource\Image\AbstractImage
     */
    protected function loadImage($filename, $unlink = false)
    {
        $imageKey = md5($filename);
        if (!isset($this->imageCache[$imageKey])) {
            $image = Image::imageWithPath($filename);
            $this->imageCache[$imageKey] = $image;
            $this->logger->info2("Loaded image '".$filename."'.");
            if ($unlink) {
                unlink($filename);
            }
        }
        return $this->imageCache[$imageKey];
    }

    /**
     * Loads a rotated version of an image.
     * @param string  $filename The filename to load.
     * @param integer $rotation The rotation to use.
     * @return \ZendPdf\Resource\Image\AbstractImage
     */
    protected function loadRotatedImage($filename, $rotation)
    {
        $this->logger->info2("Requested rotated version of '" . $filename . "', rotated by ".$rotation." degrees.");
        $name = pathinfo($filename, PATHINFO_FILENAME);
        $name .= "-".$rotation . ".png";
        $key = md5($name);
        if (!isset($this->imageCache[$key])) {
            $this->logger->info2("Rotating '" . $filename . " by " . $rotation . " degrees.");
            $content = file_get_contents($filename);
            $im = imagecreatefromstring($content);
            $im = imagerotate($im, $rotation, 0);
            imagepng($im, $name);
        }
        return $this->loadImage($name, true);
    }
}
