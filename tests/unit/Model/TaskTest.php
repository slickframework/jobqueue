<?php

/**
 * Task test case
 *
 * @package   Test\model
 * @author    Filipe Silva <silvam.filipe@gmail.com>
 * @copyright 2015 SlickFramework
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 * @since     Version 1.0.0
 */

namespace Model;

use Codeception\Util\Stub;
use UnitTester;
use Slick\JobQueue\Job\Status;
use Slick\JobQueue\Model\Task;
use Codeception\TestCase\Test;

/**
 * Task test case
 *
 * @package   Test\model
 * @author    Filipe Silva <silvam.filipe@gmail.com>
 */
class TaskTest extends Test
{

    /**
     * @var UnitTester
     */
    protected $tester;

    /**
     * Retrieve array data from a task
     * @test
     */
    public function retrieveArrayData()
    {
        $worker = Stub::construct('Slick\JobQueue\Worker', [], [
            '_jobQueue' => 'dummyJobQueue',
        ]);
        $this->tester->am('developer');
        $this->tester->amGoingTo('retrieve the data values from a task');
        $this->tester->lookForwardTo('can use it on database save actions');
        $data = [
            'type' => 'Basic',
            'data' => serialize([]),
            'created' => '2015-01-14 21:16:00',
            'notBefore' => '2015-01-14 21:20:00',
            'fetched' => '2015-01-14 21:20:14',
            'status' => Status::Queued,
            'failureMessage' => ''
        ];
        $task = new Task($data);
        $task->worker = $worker;
        $this->assertEquals($data, $task->asArray());
        $this->assertEquals('dummyJobQueue', $task->getJobQueue());
    }
}