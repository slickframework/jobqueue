<?php

/**
 * Worker test case
 *
 * @package   Test
 * @author    Filipe Silva <silvam.filipe@gmail.com>
 * @copyright 2015 SlickFramework
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 * @since     Version 1.0.0
 */

namespace Queue;

use UnitTester;
use Codeception\TestCase\Test;

/**
 * Worker test case
 *
 * @package   Test
 * @author    Filipe Silva <silvam.filipe@gmail.com>
 */
class WorkerTest extends Test
{
    /**
     * @var UnitTester
     */
    protected $tester;

    /**
     * Execute on job and run for one second
     * @test
     */
    public function runWorkerForOneSecond()
    {
        $queue = \Codeception\Util\Stub::makeEmpty('Slick\JobQueue\Queue\Database', [
            'next' => function() {
                static $count;
                if (!$count) {
                    $count = 0;
                }

                $result = [
                    0 => new  \Slick\JobQueue\Job\Basic(['type' => 'Basic']),
                    1 => null
                ];

                return isset($result[$count]) ? $result[$count++] : null;
            }
        ]);

        $worker = new \Slick\JobQueue\Worker([
            'timeout' => 1,
            'jobQueue' => $queue
        ]);

        $start = time();
        $worker->run();
        $end = time() - $start;
        $this->assertGreaterThanOrEqual(1, $end);

        $worker->exitWhenNothingToDo = true;
        $worker->run();

    }

}