<?php
namespace Goldbek\Cards\PrintPreparation;

use GetOptionKit\OptionCollection;

/**
 * Class Application
 * Serves as an entry point for the CLI application
 * @package Goldbek\Cards\PrintPreparation
 */
class Application extends \CLIFramework\Application {

    const NAME = 'PrintPreparation';
    const VERSION = '1.0.0';

    /**
     * Overridden brief function to supply a description for our CLI app.
     * @return string
     */
    public function brief()
    {
        return "This application prepares print sheets for customer orders.";
    }

    /**
     * Overridden options function to use options for every command.
     * @param OptionCollection $opts
     * @return void
     */
    public function options($opts)
    {
        parent::options($opts);
    }

    /**
     * Overridden init function to register commands to this app.
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->addCommand('generate');
    }
}
