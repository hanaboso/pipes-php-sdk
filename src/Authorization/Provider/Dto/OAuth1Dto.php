<?php declare(strict_types=1);

namespace Hanaboso\PipesPhpSdk\Authorization\Provider\Dto;

use Hanaboso\PipesPhpSdk\Application\Document\ApplicationInstall;
use Hanaboso\PipesPhpSdk\Authorization\Base\Basic\BasicApplicationInterface;
use Hanaboso\PipesPhpSdk\Authorization\Base\OAuth1\OAuth1ApplicationInterface;

/**
 * Class OAuth1Dto
 *
 * @package Hanaboso\PipesPhpSdk\Authorization\Provider\Dto
 */
final class OAuth1Dto implements OAuth1DtoInterface
{

    /**
     * @var ApplicationInstall
     */
    private ApplicationInstall $applicationInstall;

    /**
     * @var string
     */
    private string $consumerKey;

    /**
     * @var string
     */
    private string $consumerSecret;

    /**
     * @var string
     */
    private string $signatureMethod;

    /**
     * @var int
     */
    private int $authType;

    /**
     * OAuth1Dto constructor.
     *
     * @param ApplicationInstall $applicationInstall
     * @param string             $signatureMethod
     * @param int                $authType
     */
    public function __construct(
        ApplicationInstall $applicationInstall,
        string $signatureMethod = OAUTH_SIG_METHOD_HMACSHA1,
        int $authType = OAUTH_AUTH_TYPE_AUTHORIZATION
    )
    {
        $this->applicationInstall = $applicationInstall;
        $this->signatureMethod    = $signatureMethod;
        $this->authType           = $authType;
        $this->consumerKey        = $applicationInstall->getSettings()
                                    [BasicApplicationInterface::AUTHORIZATION_SETTINGS][OAuth1ApplicationInterface::CONSUMER_KEY] ?? '';
        $this->consumerSecret     = $applicationInstall->getSettings()
                                    [BasicApplicationInterface::AUTHORIZATION_SETTINGS][OAuth1ApplicationInterface::CONSUMER_SECRET] ?? '';
    }

    /**
     * @return string
     */
    public function getConsumerKey(): string
    {
        return $this->consumerKey;
    }

    /**
     * @return string
     */
    public function getConsumerSecret(): string
    {
        return $this->consumerSecret;
    }

    /**
     * @return string
     */
    public function getSignatureMethod(): string
    {
        return $this->signatureMethod;
    }

    /**
     * @return int
     */
    public function getAuthType(): int
    {
        return $this->authType;
    }

    /**
     * @return ApplicationInstall
     */
    public function getApplicationInstall(): ApplicationInstall
    {
        return $this->applicationInstall;
    }

    /**
     * @return mixed[]
     */
    public function getToken(): array
    {
        return $this->applicationInstall->getSettings()
               [BasicApplicationInterface::AUTHORIZATION_SETTINGS][BasicApplicationInterface::TOKEN] ?? [];
    }

}
