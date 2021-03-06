<?php declare(strict_types=1);

namespace Hanaboso\PipesPhpSdk\HbPFCustomNodeBundle\Exception;

use Hanaboso\Utils\Exception\PipesFrameworkExceptionAbstract;

/**
 * Class CustomNodeException
 *
 * @package Hanaboso\PipesPhpSdk\HbPFCustomNodeBundle\Exception
 */
final class CustomNodeException extends PipesFrameworkExceptionAbstract
{

    public const CUSTOM_NODE_SERVICE_NOT_FOUND = self::OFFSET + 1;

    protected const OFFSET = 1_800;

}
