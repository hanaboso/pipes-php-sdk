<?php declare(strict_types=1);

namespace Hanaboso\PipesPhpSdk\HbPFJoinerBundle\Exception;

use Hanaboso\Utils\Exception\PipesFrameworkExceptionAbstract;

/**
 * Class JoinerException
 *
 * @package Hanaboso\PipesPhpSdk\HbPFJoinerBundle\Exception
 */
final class JoinerException extends PipesFrameworkExceptionAbstract
{

    public const JOINER_SERVICE_NOT_FOUND = self::OFFSET + 1;
    public const MISSING_DATA_IN_REQUEST  = self::OFFSET + 2;

    protected const OFFSET = 1_600;

}
