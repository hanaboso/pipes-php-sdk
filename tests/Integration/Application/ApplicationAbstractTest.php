<?php declare(strict_types=1);

namespace PipesPhpSdkTests\Integration\Application;

use Exception;
use Hanaboso\CommonsBundle\Enum\ApplicationTypeEnum;
use Hanaboso\PipesPhpSdk\Application\Base\ApplicationAbstract;
use Hanaboso\PipesPhpSdk\Application\Document\ApplicationInstall;
use PipesPhpSdkTests\DatabaseTestCaseAbstract;

/**
 * Class ApplicationAbstractTest
 *
 * @package PipesPhpSdkTests\Integration\Application
 */
final class ApplicationAbstractTest extends DatabaseTestCaseAbstract
{

    /**
     * @var TestNullApplication
     */
    private TestNullApplication $application;

    /**
     * @covers \Hanaboso\PipesPhpSdk\Application\Base\ApplicationAbstract::getApplicationType
     */
    public function testGetApplicationType(): void
    {
        self::assertEquals(ApplicationTypeEnum::CRON, $this->application->getApplicationType());
    }

    /**
     * @covers \Hanaboso\PipesPhpSdk\Application\Base\ApplicationAbstract::toArray
     */
    public function testToArray(): void
    {
        self::assertEquals(
            [
                'name'               => 'Null',
                'authorization_type' => 'basic',
                'application_type'   => 'cron',
                'key'                => 'null-key',
                'description'        => 'Application for test purposes',
            ],
            $this->application->toArray(),
        );
    }

    /**
     * @covers \Hanaboso\PipesPhpSdk\Application\Base\ApplicationAbstract::getApplicationForm
     * @covers \Hanaboso\PipesPhpSdk\Application\Document\ApplicationInstall::setKey
     * @covers \Hanaboso\PipesPhpSdk\Application\Document\ApplicationInstall::setUser
     * @covers \Hanaboso\PipesPhpSdk\Application\Document\ApplicationInstall::setSettings
     *
     * @throws Exception
     */
    public function testGetApplicationForm(): void
    {
        $applicationInstall = $this->createApplicationInstall();

        self::assertEquals(3, count($this->application->getApplicationForm($applicationInstall)));
    }

    /**
     * @covers \Hanaboso\PipesPhpSdk\Application\Base\ApplicationAbstract::setApplicationSettings
     * @covers \Hanaboso\PipesPhpSdk\Application\Document\ApplicationInstall::setKey
     * @covers \Hanaboso\PipesPhpSdk\Application\Document\ApplicationInstall::setUser
     * @covers \Hanaboso\PipesPhpSdk\Application\Document\ApplicationInstall::setSettings
     *
     * @throws Exception
     */
    public function testSetApplicationSettings(): void
    {
        $applicationInstall = $this->createApplicationInstall();

        $applicationInstall = $this->application->setApplicationSettings(
            $applicationInstall,
            ['user' => 'myUsername'],
        );

        self::assertEquals(
            'myUsername',
            $applicationInstall->getSettings()[ApplicationAbstract::FORM]['user'],
        );
    }

    /**
     * @covers \Hanaboso\PipesPhpSdk\Application\Base\ApplicationAbstract::getUri
     */
    public function testGetUri(): void
    {
        self::assertEquals(147, $this->application->getUri('google:147')->getPort());
    }

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->application = self::getContainer()->get('hbpf.application.null');
    }

    /**
     * @return ApplicationInstall
     * @throws Exception
     */
    private function createApplicationInstall(): ApplicationInstall
    {
        $applicationInstall = (new ApplicationInstall())
            ->setKey('null-key')
            ->setUser('user')
            ->setSettings(
                [
                    ApplicationAbstract::FORM => [
                        'user'     => 'user12',
                        'password' => '!@#$$%%',
                    ],
                ],
            );
        $this->pfd($applicationInstall);

        return $applicationInstall;
    }

}
