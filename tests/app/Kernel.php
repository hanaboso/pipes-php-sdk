<?php declare(strict_types=1);

namespace PipesPhpSdkTests\app;

use Exception;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Exception\LoaderLoadException;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

/**
 * Class Kernel
 *
 * @package PipesPhpSdkTests\app
 * @codeCoverageIgnore
 */
final class Kernel extends BaseKernel
{

    use MicroKernelTrait;

    public const CONFIG_EXTS = '.{yaml}';

    /**
     * @return BundleInterface[]
     */
    public function registerBundles(): iterable
    {
        $contents = require sprintf('%s/tests/app/config/Bundles.php', $this->getProjectDir());
        foreach ($contents as $class => $envs) {
            if ($envs[$this->environment] ?? $envs['all'] ?? FALSE) {
                yield new $class();
            }
        }
    }

    /**
     * @param ContainerBuilder $container
     * @param LoaderInterface  $loader
     *
     * @throws Exception
     */
    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
        $container->addResource(new FileResource(sprintf('%s/tests/app/config/Bundles.php', $this->getProjectDir())));
        $container->setParameter('container.dumper.inline_class_loader', TRUE);
        $confDir = $this->getConfigDir();
        $loader->load(sprintf('%s/{packages}/*%s', $confDir, self::CONFIG_EXTS), 'glob');
        $loader->load(sprintf('%s/{packages}/%s/**/*%s', $confDir, $this->environment, self::CONFIG_EXTS), 'glob');
        $loader->load(sprintf('%s/{services}%s', $confDir, self::CONFIG_EXTS), 'glob');
        $loader->load(sprintf('%s/{services}_%s%s', $confDir, $this->environment, self::CONFIG_EXTS), 'glob');
        $loader->load(sprintf('%s/{modules}/*%s', $confDir, self::CONFIG_EXTS), 'glob');
    }

    /**
     * @param RouteCollectionBuilder $routes
     *
     * @throws LoaderLoadException
     */
    protected function configureRoutes(RouteCollectionBuilder $routes): void
    {
        $confDir = $this->getConfigDir();
        $routes->import(sprintf('%s/{routes}/*%s', $confDir, self::CONFIG_EXTS), '/', 'glob');
        $routes->import(sprintf('%s/{routes}/%s/**/*%s', $confDir, $this->environment, self::CONFIG_EXTS), '/', 'glob');
        $routes->import(sprintf('%s/{routes}%s', $confDir, self::CONFIG_EXTS), '/', 'glob');
    }

    /**
     * @return string
     */
    private function getConfigDir(): string
    {
        return sprintf('%s/tests/app/config', $this->getProjectDir());
    }

}