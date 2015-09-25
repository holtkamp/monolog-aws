<?php
/**
 * Created by mcfedr on 07/10/14 20:44
 */

namespace Mcfedr\Monolog\Handler;

use Monolog\Handler\TestHandler;
use Monolog\Logger;

class SesHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * This tests that no exceptions come out
     */
    public function testSesHandler()
    {
        $sesMock = $this->getMock('Aws\Ses\SesClient', ['sendEmail'], [], '', false);

        $sesMock->expects($this->once())
            ->method('sendEmail');

        $logger = new Logger('test', [
            new SesHandler("test@example.com", "test", "test@example.com", $sesMock)
        ]);
        $logger->error('The error');
    }

    /**
     * This tests that the handler pushes out exceptions
     */
    public function testSesWithLoggerHandler()
    {
        $sesMock = $this->getMock('Aws\Ses\SesClient', ['sendEmail'], [], '', false);

        $sesMock->expects($this->once())
            ->method('sendEmail')
            ->will($this->throwException($this->getMockBuilder('Aws\Ses\Exception\SesException')->disableOriginalConstructor()->getMock()));

        $testHandler = new TestHandler();
        $handlerLogger = new Logger('handler-logger', [$testHandler]);
        $handler = new SesHandler("test@example.com", "test", "test@example.com", $sesMock);
        $handler->setLogger($handlerLogger);
        $logger = new Logger('test', [$handler]);
        $logger->error('The error');

        $this->assertTrue($testHandler->hasErrorRecords());
    }
}
