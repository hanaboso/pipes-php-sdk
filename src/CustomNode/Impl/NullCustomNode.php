<?php declare(strict_types=1);

namespace Hanaboso\PipesPhpSdk\CustomNode\Impl;

use Hanaboso\CommonsBundle\Process\ProcessDto;
use Hanaboso\PipesPhpSdk\CustomNode\CustomNodeAbstract;
use Hanaboso\Utils\System\PipesHeaders;

/**
 * Class NullCustomNode
 *
 * @package Hanaboso\PipesPhpSdk\CustomNode\Impl
 */
final class NullCustomNode extends CustomNodeAbstract
{

    /**
     * @param ProcessDto $dto
     *
     * @return ProcessDto
     */
    public function processAction(ProcessDto $dto): ProcessDto
    {
        $dto->addHeader(PipesHeaders::createKey(PipesHeaders::RESULT_MESSAGE), 'Null worker resending data.');

        return $dto;
    }

}
