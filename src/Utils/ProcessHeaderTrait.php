<?php declare(strict_types=1);

namespace Hanaboso\PipesPhpSdk\Utils;

use Hanaboso\CommonsBundle\Process\ProcessDto;
use Hanaboso\PipesPhpSdk\Connector\Exception\ConnectorException;
use Hanaboso\PipesPhpSdk\Connector\Traits\ProcessExceptionTrait;
use Hanaboso\Utils\System\PipesHeaders;

/**
 * Trait ProcessHeaderTrait
 *
 * @package Hanaboso\PipesPhpSdk\Utils
 */
trait ProcessHeaderTrait
{

    use ProcessExceptionTrait;

    /**
     * @param ProcessDto $dto
     * @param string     $key
     * @param mixed[]    $headers
     * @param bool       $throw
     *
     * @return string
     * @throws ConnectorException
     */
    protected function getHeaderByKey(ProcessDto $dto, string $key, array $headers = [], bool $throw = TRUE): string
    {
        $headers = $headers ?: $dto->getHeaders();
        $header  = PipesHeaders::get($key, $headers) ?: '';

        if (!$header && $throw) {
            throw $this->createMissingHeaderException($key);
        }

        return $header;
    }

    /**
     * @param ProcessDto $dto
     * @param string     $key
     * @param string     $value
     *
     * @return ProcessDto
     */
    protected function setHeader(ProcessDto $dto, string $key, string $value): ProcessDto
    {
        return $dto->addHeader(PipesHeaders::createKey($key), $value);
    }

}
