<?php declare(strict_types=1);

namespace PipesPhpSdkTests\Controller\HbPFTableParserBundle\Controller;

use Doctrine\ODM\MongoDB\Mapping\MappingException;
use Exception;
use Hanaboso\CommonsBundle\Exception\FileStorageException;
use Hanaboso\PipesPhpSdk\HbPFMapperBundle\Handler\MapperHandler;
use Hanaboso\PipesPhpSdk\HbPFTableParserBundle\Handler\TableParserHandler;
use Hanaboso\PipesPhpSdk\Parser\Exception\TableParserException;
use Hanaboso\Utils\Exception\PipesFrameworkException;
use Hanaboso\Utils\String\Json;
use PHPUnit\Framework\MockObject\MockObject;
use PipesPhpSdkTests\ControllerTestCaseAbstract;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ApiControllerTest
 *
 * @package PipesPhpSdkTests\Controller\HbPFTableParserBundle\Controller
 */
final class ApiControllerTest extends ControllerTestCaseAbstract
{

    /**
     * @covers \Hanaboso\PipesPhpSdk\HbPFTableParserBundle\Controller\TableParserController::toJsonAction
     */
    public function testToJson(): void
    {
        $response = $this->sendPost(
            '/parser/csv/to/json',
            [
                'file_id' => sprintf('%s/../../../Integration/Parser/data/input-10.csv', __DIR__),
            ]
        );

        self::assertEquals(200, $response->status);
        self::assertEquals(
            Json::decode(
                (string) file_get_contents(__DIR__ . '/../../../Integration/Parser/data/output-10.json')
            ),
            $response->content
        );
    }

    /**
     * @covers \Hanaboso\PipesPhpSdk\HbPFTableParserBundle\Controller\TableParserController::toJsonAction
     * @throws Exception
     */
    public function testToJsonActionErr(): void
    {
        $this->prepareTableParserErr('parseToJson', new MappingException());

        $this->client->request('POST', '/parser/csv/to/json');

        /** @var Response $response */
        $response = $this->client->getResponse();
        self::assertEquals(500, $response->getStatusCode());
    }

    /**
     * @covers \Hanaboso\PipesPhpSdk\HbPFTableParserBundle\Controller\TableParserController::toJsonAction
     * @throws Exception
     */
    public function testToJsonActionErr2(): void
    {
        $this->prepareTableParserErr('parseToJson', new PipesFrameworkException());
        $this->client->request('POST', '/parser/csv/to/json');

        /** @var Response $response */
        $response = $this->client->getResponse();
        self::assertEquals(500, $response->getStatusCode());
    }

    /**
     * @covers \Hanaboso\PipesPhpSdk\HbPFTableParserBundle\Controller\TableParserController::toJsonAction
     */
    public function testToJsonNotFound(): void
    {
        $response = $this->sendPost('/parser/csv/to/json', ['file_id' => '']);

        self::assertEquals(500, $response->status);
        $content = $response->content;
        self::assertEquals(FileStorageException::class, $content->type);
        self::assertEquals(1_501, $content->errorCode);
    }

    /**
     * @covers \Hanaboso\PipesPhpSdk\HbPFTableParserBundle\Controller\TableParserController::toJsonTestAction
     */
    public function testToJsonTest(): void
    {
        $response = $this->sendGet('/parser/csv/to/json/test');

        self::assertEquals(200, $response->status);
    }

    /**
     * @covers \Hanaboso\PipesPhpSdk\HbPFTableParserBundle\Controller\TableParserController::toJsonTestAction
     * @throws Exception
     */
    public function testToJsonTestErr(): void
    {
        $this->prepareTableParserErr('parseToJsonTest', new Exception());
        $response = $this->sendGet('/parser/csv/to/json/test');

        self::assertEquals(500, $response->status);
    }

    /**
     * @covers \Hanaboso\PipesPhpSdk\HbPFTableParserBundle\Controller\TableParserController::fromJsonAction
     */
    public function testFromJson(): void
    {
        $response = $this->sendPost(
            '/parser/json/to/csv',
            [
                'file_id' => sprintf('%s/../../../Integration/Parser/data/output-10.json', __DIR__),
            ]
        );

        self::assertEquals(200, $response->status);
        self::assertRegExp('#\/tmp\/\d+\.\d+\.csv#i', $response->content[0]);
    }

    /**
     * @covers \Hanaboso\PipesPhpSdk\HbPFTableParserBundle\Controller\TableParserController::fromJsonAction
     */
    public function testToFromNotFound(): void
    {
        $response = $this->sendPost('/parser/json/to/csv', ['file_id' => '']);
        $content  = $response->content;

        self::assertEquals(500, $response->status);
        self::assertEquals(FileStorageException::class, $content->type);
        self::assertEquals(1_501, $content->errorCode);
    }

    /**
     * @covers \Hanaboso\PipesPhpSdk\HbPFTableParserBundle\Controller\TableParserController::fromJsonAction
     */
    public function testFromJsonNotFoundWriter(): void
    {
        $response = $this->sendPost(
            '/parser/json/to/unknown',
            [
                'file_id' => sprintf('%s/../../../Integration/Parser/data/output-10.json', __DIR__),
            ]
        );
        $content  = $response->content;

        self::assertEquals(500, $response->status);
        self::assertEquals(TableParserException::class, $content->type);
        self::assertEquals(801, $content->errorCode);
    }

    /**
     * @covers \Hanaboso\PipesPhpSdk\HbPFTableParserBundle\Controller\TableParserController::fromJsonTestAction
     * @covers \Hanaboso\PipesPhpSdk\HbPFTableParserBundle\Handler\TableParserHandler::parseFromJsonTest
     * @covers \Hanaboso\PipesPhpSdk\Parser\TableParser::createWriter
     */
    public function testFromTestJson(): void
    {
        $response = $this->sendGet('/parser/json/to/csv/test');

        self::assertEquals(200, $response->status);
    }

    /**
     * @covers \Hanaboso\PipesPhpSdk\HbPFTableParserBundle\Controller\TableParserController::fromJsonTestAction
     * @covers \Hanaboso\PipesPhpSdk\HbPFTableParserBundle\Handler\TableParserHandler::parseFromJsonTest
     * @covers \Hanaboso\PipesPhpSdk\Parser\TableParser::createWriter
     */
    public function testFromTestJsonNotFound(): void
    {
        $response = $this->sendGet('/parser/json/to/csv/test');

        self::assertEquals(200, $response->status);
    }

    /**
     * @covers \Hanaboso\PipesPhpSdk\HbPFTableParserBundle\Controller\TableParserController::fromJsonTestAction
     * @throws Exception
     */
    public function testFromTestJsonNotFoundErr(): void
    {
        $this->prepareTableParserErr('parseFromJsonTest', new TableParserException());
        $response = $this->sendGet('/parser/json/to/csv/test');

        self::assertEquals(500, $response->status);
    }

    /**
     *
     */
    public function testFromJsonTestNotFoundWriter(): void
    {
        $response = $this->sendPost(
            '/parser/json/to/unknown',
            [
                'file_id' => sprintf('%s/../../../Integration/Parser/data/output-10.json', __DIR__),
            ]
        );
        $content  = $response->content;

        self::assertEquals(500, $response->status);
        self::assertEquals(TableParserException::class, $content->type);
        self::assertEquals(801, $content->errorCode);
    }

    /**
     * @param string       $url
     * @param mixed[]      $parameters
     * @param mixed[]|null $content
     *
     * @return object
     */
    protected function sendPost(string $url, array $parameters, ?array $content = NULL): object
    {
        $this->client->request(
            'POST',
            $url,
            $parameters,
            [],
            [],
            $content ? Json::encode($content) : ''
        );
        /** @var Response $response */
        $response = $this->client->getResponse();
        $res      = Json::decode((string) $response->getContent());

        if (isset($res['error_code'])) {
            return parent::sendPost($url, $parameters, $content);
        }

        return (object) [
            'status'  => $response->getStatusCode(),
            'content' => Json::decode((string) $response->getContent()),
        ];
    }

    /**
     * @param string $methodName
     * @param mixed  $returnValue
     *
     * @throws Exception
     */
    private function prepareTableParserErr(string $methodName, $returnValue): void
    {
        /** @var MapperHandler|MockObject $mapperHandlerMock */
        $mapperHandlerMock = self::createMock(TableParserHandler::class);
        $mapperHandlerMock
            ->method($methodName)
            ->willThrowException($returnValue);

        /** @var ContainerInterface $container */
        $container = $this->client->getContainer();
        $container->set('hbpf.parser.table.handler', $mapperHandlerMock);
    }

}
