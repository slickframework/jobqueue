<?php

/**
 * Queue command
 *
 * @package   Slick\JobQueue\Job
 * @author    Filipe Silva <silvam.filipe@gmail.com>
 * @copyright 2015 SlickFramework
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 * @since     Version 1.0.0
 */

namespace Slick\JobQueue\Console;

use Monolog\Logger;
use Slick\JobQueue\Logger\Handler\ConsoleHandler;
use Slick\JobQueue\Worker;
use Psr\Log\LoggerInterface;
use Slick\JobQueue\JobQueueInterface;
use Slick\Configuration\Driver\DriverInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


/**
 * Queue command
 *
 * @package   Slick\JobQueue\Job
 * @author    Filipe Silva <silvam.filipe@gmail.com>
 */
class QueueCommand extends Command
{

    /**
     * @var JobQueueInterface
     */
    protected $_jobQueue;

    /**
     * @var DriverInterface
     */
    protected $_config;

    /**
     * @var string
     */
    const CFG_KEY = 'worker';

    /**
     * Constructor.
     *
     * @param string|null $name The name of the command; passing null means it must be set in configure()
     *
     * @throws \LogicException When the command name is empty
     *
     * @api
     */
    public function __construct(JobQueueInterface $queue, DriverInterface $cfg, $name = null)
    {
        parent::__construct($name);
        $this->_jobQueue = $queue;
        $this->_config = $cfg;
    }

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this
            ->setName("queue:run")
            ->setDescription("Runs a job queue worker.");
    }

    /**
     * Executes the current command.
     *
     * This method is not abstract because you can use this class
     * as a concrete class. In this case, instead of defining the
     * execute() method, you set the code to execute by passing
     * a Closure to the setCode() method.
     *
     * @param InputInterface $input An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     *
     * @return null|integer null or 0 if everything went fine, or an error code
     *
     * @see    setCode()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $logger = new Logger('Queue command');
        $handler = new ConsoleHandler();
        $handler->setOutput($output);
        $logger->pushHandler($handler);

        $options = array_merge($this->_config->get(self::CFG_KEY), [
            'jobQueue' =>  $this->_jobQueue,
            'logger' => $logger
        ]);

        $worker = new Worker($options);
        $worker->jobQueue = $this->_jobQueue;


        $worker->run();
    }

}