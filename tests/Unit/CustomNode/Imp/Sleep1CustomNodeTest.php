<?php declare(strict_types=1);

namespace PipesPhpSdkTests\Unit\CustomNode\Imp;

use Hanaboso\CommonsBundle\Process\ProcessDto;
use Hanaboso\PipesPhpSdk\CustomNode\Impl\Sleep1CustomNode;
use PipesPhpSdkTests\KernelTestCaseAbstract;

/**
 * Class Sleep1CustomNodeTest
 *
 * @package PipesPhpSdkTests\Unit\CustomNode\Imp
 */
final class Sleep1CustomNodeTest extends KernelTestCaseAbstract
{

    /**
     * @covers \Hanaboso\PipesPhpSdk\CustomNode\Impl\Sleep1CustomNode::processAction
     */
    public function testProcess(): void
    {
        $start = microtime(TRUE);
        (new Sleep1CustomNode())->processAction(new ProcessDto());
        $time = microtime(TRUE) - $start;
        self::assertGreaterThan(1, $time);
    }

}
