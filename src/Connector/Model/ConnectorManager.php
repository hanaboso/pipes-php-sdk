<?php declare(strict_types=1);

namespace Hanaboso\PipesPhpSdk\Connector\Model;

use Hanaboso\CommonsBundle\Process\ProcessDto;
use Hanaboso\PipesPhpSdk\Connector\ConnectorInterface;
use Hanaboso\PipesPhpSdk\Connector\Exception\ConnectorException;
use Hanaboso\PipesPhpSdk\Utils\ProcessDtoFactory;
use Hanaboso\Utils\System\PipesHeaders;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ConnectorManager
 *
 * @package Hanaboso\PipesPhpSdk\Connector\Model
 */
final class ConnectorManager
{

    /**
     * @param ConnectorInterface $conn
     * @param Request            $request
     *
     * @return ProcessDto
     * @throws ConnectorException
     */
    public function processAction(ConnectorInterface $conn, Request $request): ProcessDto
    {
        $dto = ProcessDtoFactory::createFromRequest($request);
        $key = $conn->getApplicationKey();
        if ($key) {
            $headers                                                     = $dto->getHeaders();
            $headers[PipesHeaders::createKey(PipesHeaders::APPLICATION)] = [$key];
            $dto->setHeaders($headers);
        }

        return $conn->processAction($dto);
    }

}
