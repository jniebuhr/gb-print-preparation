<?php
namespace Goldbek\Cards\PrintPreparation\Command;

use CLIFramework\Command;
use CLIFramework\Logger;
use GetOptionKit\OptionCollection;
use Goldbek\Cards\PrintPreparation\Service\GenerationService;
use Goldbek\Cards\PrintPreparation\Util\ConfigHelper;

/**
 * Class GenerateCommand
 * The generate command is used to generate the print sheets for an input job.
 * @package Goldbek\Cards\PrintPreparation\Command
 */
class GenerateCommand extends Command {

    /**
     * @var GenerationService
     */
    protected $service;

    /**
     * Overridden brief function to supply a description for this command.
     * @return string
     */
    public function brief()
    {
        return "Executes a supplied job.";
    }

    /**
     * @param OptionCollection $opts
     */
    public function options($opts)
    {
        parent::options($opts);
        $opts->add('c|config', 'The job config to use.')
            ->isa('string')
            ->valueName('configFile')
            ->required();
    }

    /**
     * Overridden init function to initialize some components of this command.
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->service = new GenerationService();
        $this->service->injectLogger($this->getLogger());
    }


    /**
     * Main entrypoint for this command.
     * Reads the config and executes the job.
     * @return void
     */
    public function execute()
    {
        /**
         * @var $logger Logger
         */
        $logger = $this->getLogger();
        $configFile = $this->options->config;
        if (is_null($configFile) || empty($configFile)) {
            $logger->error('You did not supply a config. Please use a config by adding -c <configFile> to your command.');
            return 1;
        }
        $job = null;
        try {
            $job = ConfigHelper::loadJob($configFile);
        } catch(\Exception $exc) {
            $logger->error('An error occured while loading the config file: ' . $exc->getMessage());
            return 1;
        }
        if ($job == null) {
            $logger->error('Had trouble loading the config. Please check your config file and try again.');;
            return 1;
        }
        if ($job->getMode() == 'default') {
            $result = $this->service->execute($job);
        } else {
            $logger->error('The specified mode is not yet implemented.');
            return 1;
        }
    }
}
