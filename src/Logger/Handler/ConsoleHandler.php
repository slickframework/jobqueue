<?php

/**
 * Monolog Logger handler
 *
 * @package   Slick\JobQueue\Logger\Handler
 * @author    Filipe Silva <silvam.filipe@gmail.com>
 * @copyright 2015 SlickFramework
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 * @since     Version 1.0.0
 */

namespace Slick\JobQueue\Logger\Handler;

use Monolog\Handler\AbstractProcessingHandler;
use Slick\JobQueue\Logger\Formatter\ConsoleFormatter;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Monolog Logger handler
 *
 * @package   Slick\JobQueue\Logger\Handler
 * @author    Filipe Silva <silvam.filipe@gmail.com>
 */
class ConsoleHandler extends AbstractProcessingHandler
{

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * Writes the record down to the log of the implementing handler
     *
     * @param  array $record
     * @return void
     */
    protected function write(array $record)
    {
        $record = $this->getFormatter()->format($record);
        $this->output->writeln($record['formatted']);
    }

    /**
     * {@inheritdoc}
     */
    public function getFormatter()
    {
        if (!$this->formatter) {
            $this->formatter = new ConsoleFormatter();
        }

        return $this->formatter;
    }

    /**
     * Returns the console output object
     *
     * @param OutputInterface $output
     * @return ConsoleHandler
     */
    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;
        return $this;
    }
}