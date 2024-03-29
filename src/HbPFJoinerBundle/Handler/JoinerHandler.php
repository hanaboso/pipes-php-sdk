<?php declare(strict_types=1);

namespace Hanaboso\PipesPhpSdk\HbPFJoinerBundle\Handler;

use Hanaboso\PipesPhpSdk\HbPFJoinerBundle\Exception\JoinerException;
use Hanaboso\PipesPhpSdk\HbPFJoinerBundle\Loader\JoinerLoader;

/**
 * Class JoinerHandler
 *
 * @package Hanaboso\PipesPhpSdk\HbPFJoinerBundle\Handler
 */
final class JoinerHandler
{

    /**
     * JoinerHandler constructor.
     *
     * @param JoinerLoader $loader
     */
    function __construct(private JoinerLoader $loader)
    {
    }

    /**
     * @param string  $joinerId
     * @param mixed[] $data
     *
     * @return mixed[]
     * @throws JoinerException
     */
    public function processJoiner(string $joinerId, array $data): array
    {
        $joiner = $this->loader->get($joinerId);

        return $joiner->process($data['data'], $data['count']);
    }

    /**
     * @param string  $joinerId
     * @param mixed[] $data
     *
     * @throws JoinerException
     */
    public function processJoinerTest(string $joinerId, array $data): void
    {
        $this->loader->get($joinerId);

        if (!isset($data['data'])) {
            throw new JoinerException(
                'Data under \'data\' key are missing in request.',
                JoinerException::MISSING_DATA_IN_REQUEST,
            );
        }
        if (!isset($data['count'])) {
            throw new JoinerException(
                'Total data count under \'count\' key is missing in request.',
                JoinerException::MISSING_DATA_IN_REQUEST,
            );
        }
    }

    /**
     * @return mixed[]
     */
    public function getJoiners(): array
    {
        return $this->loader->getAllJoiners();
    }

}
