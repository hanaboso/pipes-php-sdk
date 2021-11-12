<?php declare(strict_types=1);

namespace PipesPhpSdkTests\Integration\Application;

use Hanaboso\CommonsBundle\Transport\Curl\CurlException;
use Hanaboso\CommonsBundle\Transport\Curl\Dto\RequestDto;
use Hanaboso\PipesPhpSdk\Application\Document\ApplicationInstall;
use Hanaboso\PipesPhpSdk\Application\Model\Form\Field;
use Hanaboso\PipesPhpSdk\Application\Model\Form\Form;
use Hanaboso\PipesPhpSdk\Application\Utils\SynchronousAction;
use Hanaboso\PipesPhpSdk\Authorization\Base\Basic\BasicApplicationAbstract;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class TestNullApplication
 *
 * @package PipesPhpSdkTests\Integration\Application
 */
final class TestNullApplication extends BasicApplicationAbstract
{

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'null-key';
    }

    /**
     * @return string
     */
    public function getPublicName(): string
    {
        return 'Null';
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return 'Application for test purposes';
    }

    /**
     * @SynchronousAction()
     * @return string
     */
    public function testSynchronous(): string
    {
        return 'ok';
    }

    /**
     * @SynchronousAction()
     * @param Request $r
     *
     * @return mixed[]
     */
    public function returnBody(Request $r): array
    {
        return $r->request->all();
    }

    /**
     * @param ApplicationInstall $applicationInstall
     * @param string             $method
     * @param string|null        $url
     * @param string|null        $data
     *
     * @return RequestDto
     * @throws CurlException
     */
    public function getRequestDto(
        ApplicationInstall $applicationInstall,
        string $method,
        ?string $url = NULL,
        ?string $data = NULL,
    ): RequestDto
    {
        $applicationInstall;

        $request = new RequestDto($method, $this->getUri($url));
        $request->setHeaders(
            [
                'Content-Type' => 'application/vnd.shoptet.v1.0',
                'Accept'       => 'application/json',
            ],
        );
        if (isset($data)) {
            $request->setBody($data);
        }

        return $request;
    }

    /**
     * @return Form
     */
    public function getSettingsForm(): Form
    {
        $form = new Form();

        return $form
            ->addField(new Field(Field::TEXT, 'user', 'Username', NULL, TRUE))
            ->addField(new Field(Field::PASSWORD, 'password', 'Password', NULL, TRUE))
            ->addField(new Field(Field::TEXT, 'token', 'Token', NULL, TRUE));
    }

}
