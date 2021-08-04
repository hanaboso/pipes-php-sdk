<?php declare(strict_types=1);

namespace PipesPhpSdkTests\Unit\HbPFJoinerBundle\Handler;

use Exception;
use Hanaboso\PipesPhpSdk\HbPFJoinerBundle\Exception\JoinerException;
use PipesPhpSdkTests\KernelTestCaseAbstract;

/**
 * Class JoinerHandlerTest
 *
 * @package PipesPhpSdkTests\Unit\HbPFJoinerBundle\Handler
 */
final class JoinerHandlerTest extends KernelTestCaseAbstract
{

    /**
     * @covers \Hanaboso\PipesPhpSdk\HbPFJoinerBundle\Handler\JoinerHandler::processJoiner

     * @throws Exception
     */
    public function testJoin(): void
    {
        $handler = self::getContainer()->get('hbpf.handler.joiner');

        $data = [
            'data' => [],
        ];

        self::expectException(JoinerException::class);
        self::expectExceptionCode(JoinerException::MISSING_DATA_IN_REQUEST);
        $handler->processJoinerTest('null', $data);

        $data['count'] = 3;
        $handler->processJoiner('null', $data);
    }

}
