<?php declare(strict_types=1);

namespace Hanaboso\PipesPhpSdk\Batch;

use Hanaboso\CommonsBundle\Process\ProcessDto;
use Hanaboso\PipesPhpSdk\Application\Base\ApplicationInterface;
use Hanaboso\PipesPhpSdk\Batch\Exception\BatchException;
use Hanaboso\Utils\Exception\PipesFrameworkException;
use Hanaboso\Utils\String\Json;

/**
 * Class BatchAbstract
 *
 * @package Hanaboso\PipesPhpSdk\Batch
 */
abstract class BatchAbstract implements BatchInterface
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
     * @return BatchInterface
     */
    public function setApplication(ApplicationInterface $application): BatchInterface
    {
        $this->application = $application;

        return $this;
    }

    /**
     * @return ApplicationInterface
     * @throws BatchException
     */
    public function getApplication(): ApplicationInterface
    {
        if ($this->application) {
            return $this->application;
        }

        throw new BatchException('Application has not set.', BatchException::MISSING_APPLICATION);
    }

    /**
     * @return string|null
     */
    public function getApplicationKey(): ?string
    {
        if ($this->application) {
            return $this->application->getName();
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
