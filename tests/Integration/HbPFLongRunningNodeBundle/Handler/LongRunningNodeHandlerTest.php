<?php declare(strict_types=1);

namespace PipesPhpSdkTests\Integration\HbPFLongRunningNodeBundle\Handler;

use Exception;
use Hanaboso\MongoDataGrid\GridRequestDto;
use Hanaboso\PipesPhpSdk\HbPFLongRunningNodeBundle\Handler\LongRunningNodeHandler;
use Hanaboso\PipesPhpSdk\LongRunningNode\Document\LongRunningNodeData;
use Hanaboso\PipesPhpSdk\LongRunningNode\Exception\LongRunningNodeException;
use Hanaboso\Utils\System\PipesHeaders;
use PipesPhpSdkTests\DatabaseTestCaseAbstract;

/**
 * Class LongRunningNodeHandlerTest
 *
 * @package PipesPhpSdkTests\Integration\HbPFLongRunningNodeBundle\Handler
 */
final class LongRunningNodeHandlerTest extends DatabaseTestCaseAbstract
{

    /**
     * @var LongRunningNodeHandler
     */
    private LongRunningNodeHandler $handler;

    /**
     * @covers \Hanaboso\PipesPhpSdk\HbPFLongRunningNodeBundle\Handler\LongRunningNodeHandler
     * @covers \Hanaboso\PipesPhpSdk\HbPFLongRunningNodeBundle\Handler\LongRunningNodeHandler::process
     *
     * @throws Exception
     */
    public function testProcess(): void
    {
        $node = new LongRunningNodeData();
        $this->pfd($node);
        $dto = $this->handler->process(
            'null',
            ['data' => 'data'],
            [
                PipesHeaders::createKey(LongRunningNodeData::DOCUMENT_ID_HEADER) => $node->getId(),
                PipesHeaders::createKey(PipesHeaders::PF_STOP)                   => '200',
            ],
        );

        self::assertEquals(['pf-result-code' => '200'], $dto->getHeaders());
    }

    /**
     * @covers \Hanaboso\PipesPhpSdk\HbPFLongRunningNodeBundle\Handler\LongRunningNodeHandler::process
     *
     * @throws Exception
     */
    public function testProcessErr(): void
    {
        self::expectException(LongRunningNodeException::class);
        self::expectExceptionCode(LongRunningNodeException::LONG_RUNNING_DOCUMENT_NOT_FOUND);
        $this->handler->process(
            'null',
            ['data' => 'data'],
            [
                PipesHeaders::createKey(LongRunningNodeData::DOCUMENT_ID_HEADER) => '1',
                PipesHeaders::createKey(PipesHeaders::PF_STOP)                   => '200',
            ],
        );
    }

    /**
     * @covers \Hanaboso\PipesPhpSdk\HbPFLongRunningNodeBundle\Handler\LongRunningNodeHandler::test
     *
     * @throws Exception
     */
    public function testTest(): void
    {
        $result = $this->handler->test('null');

        self::assertEquals([], $result);
    }

    /**
     * @covers \Hanaboso\PipesPhpSdk\HbPFLongRunningNodeBundle\Handler\LongRunningNodeHandler::getTasksById
     * @covers \Hanaboso\PipesPhpSdk\LongRunningNode\Model\LongRunningNodeFilter::prepareSearchQuery
     *
     * @throws Exception
     */
    public function testGetTasksById(): void
    {
        self::assertEquals(
            [
                'items'  => [],
                'filter' => [],
                'sorter' => [],
                'search' => NULL,
                'paging' => [
                    'page'         => 1,
                    'itemsPerPage' => 10,
                    'total'        => 0,
                    'nextPage'     => 1,
                    'lastPage'     => 1,
                    'previousPage' => 1,
                ],
            ],
            $this->handler->getTasksById(new GridRequestDto([]), '1', 'null'),
        );
    }

    /**
     * @covers \Hanaboso\PipesPhpSdk\HbPFLongRunningNodeBundle\Handler\LongRunningNodeHandler::getTasks
     * @covers \Hanaboso\PipesPhpSdk\LongRunningNode\Model\LongRunningNodeFilter::prepareSearchQuery
     *
     * @throws Exception
     */
    public function testGetTasks(): void
    {
        self::assertEquals(
            [
                'items'  => [],
                'filter' => [],
                'sorter' => [],
                'search' => NULL,
                'paging' => [
                    'page'         => 1,
                    'itemsPerPage' => 10,
                    'total'        => 0,
                    'nextPage'     => 1,
                    'lastPage'     => 1,
                    'previousPage' => 1,
                ],
            ],
            $this->handler->getTasks(new GridRequestDto([]), '1', 'null'),
        );
    }

    /**
     * @covers \Hanaboso\PipesPhpSdk\HbPFLongRunningNodeBundle\Handler\LongRunningNodeHandler::getAllLongRunningNodes
     */
    public function testGetAllLongRunningNodes(): void
    {
        self::assertEquals(['null'], $this->handler->getAllLongRunningNodes());
    }

    /**
     * @covers \Hanaboso\PipesPhpSdk\HbPFLongRunningNodeBundle\Handler\LongRunningNodeHandler::updateLongRunningNode
     */
    public function testUpdateRunningNode(): void
    {
        $node = new LongRunningNodeData();
        $node
            ->setParentId('1')
            ->setTopologyName('topology')
            ->setCorrelationId('2')
            ->setSequenceId('3')
            ->setProcessId('7')
            ->setState('state')
            ->setAuditLogs([])
            ->setUpdatedBy('4')
            ->setTopologyId('5')
            ->setNodeId('6')
            ->setNodeName('node')
            ->setData('data')
            ->setContentType('string');
        $this->pfd($node);

        $result = $this->handler->updateLongRunningNode($node->getId(), ['data' => ['foo' => 'bar']]);
        self::assertEquals('{"foo":"bar"}', $result['data']);
    }

    /**
     * @covers \Hanaboso\PipesPhpSdk\HbPFLongRunningNodeBundle\Handler\LongRunningNodeHandler::updateLongRunningNode
     */
    public function testUpdateRunningNodeErr(): void
    {
        self::expectException(LongRunningNodeException::class);
        self::expectExceptionCode(LongRunningNodeException::LONG_RUNNING_DOCUMENT_NOT_FOUND);
        $this->handler->updateLongRunningNode('123', ['data' => ['foo' => 'bar']]);
    }

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->handler = self::$container->get('hbpf.handler.long_running');
    }

}
