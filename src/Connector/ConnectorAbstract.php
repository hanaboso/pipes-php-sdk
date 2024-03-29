<?php declare(strict_types=1);

namespace Hanaboso\PipesPhpSdk\Connector;

use Hanaboso\CommonsBundle\Process\ProcessDto;
use Hanaboso\PipesPhpSdk\Application\Base\ApplicationInterface;
use Hanaboso\PipesPhpSdk\Connector\Exception\ConnectorException;
use Hanaboso\Utils\Exception\PipesFrameworkException;
use Hanaboso\Utils\String\Json;

/**
 * Class ConnectorAbstract
 *
 * @package Hanaboso\PipesPhpSdk\Connector
 */
abstract class ConnectorAbstract implements ConnectorInterface
{

    /**
     * @var ApplicationInterface|null
     */
    protected ?ApplicationInterface $application = NULL;

    /**
     * @var mixed[]
     */
    protected array $okStatuses = [
        200,
        201,
    ];

    /**
     * @var mixed[]
     */
    protected array $badStatuses = [
        409,
        400,
    ];

    /**
     * @param int         $statusCode
     * @param ProcessDto  $dto
     * @param string|null $message
     *
     * @return bool
     * @throws PipesFrameworkException
     */
    public function evaluateStatusCode(int $statusCode, ProcessDto $dto, ?string $message = NULL): bool
    {
        if (in_array($statusCode, $this->okStatuses, TRUE)) {
            return TRUE;
        }

        $dto->setStopProcess(ProcessDto::STOP_AND_FAILED, $message);

        return FALSE;
    }

    /**
     * @param ApplicationInterface $application
     *
     * @return ConnectorInterface
     */
    public function setApplication(ApplicationInterface $application): ConnectorInterface
    {
        $this->application = $application;

        return $this;
    }

    /**
     * @return ApplicationInterface
     * @throws ConnectorException
     */
    public function getApplication(): ApplicationInterface
    {
        if ($this->application) {
            return $this->application;
        }

        throw new ConnectorException('Application has not set.', ConnectorException::MISSING_APPLICATION);
    }

    /**
     * @return string|null
     */
    public function getApplicationKey(): ?string
    {
        if ($this->application) {
            return $this->application->getKey();
        }

        return NULL;
    }

    /**
     * @param ProcessDto $dto
     *
     * @return mixed[]
     */
    protected function getJsonContent(ProcessDto $dto): array
    {
        return Json::decode($dto->getData());
    }

    /**
     * @param ProcessDto $dto
     * @param mixed[]    $content
     *
     * @return ProcessDto
     */
    protected function setJsonContent(ProcessDto $dto, array $content): ProcessDto
    {
        return $dto->setData(Json::encode($content));
    }

}
