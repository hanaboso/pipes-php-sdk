<?php declare(strict_types=1);

namespace Hanaboso\PipesPhpSdk\Application\Repository;

use Doctrine\ODM\MongoDB\Repository\DocumentRepository;
use Hanaboso\CommonsBundle\Process\ProcessDto;
use Hanaboso\PipesPhpSdk\Application\Document\ApplicationInstall;
use Hanaboso\PipesPhpSdk\Application\Exception\ApplicationInstallException;
use Hanaboso\Utils\Date\DateTimeUtils;
use Hanaboso\Utils\Exception\DateTimeException;
use Hanaboso\Utils\System\PipesHeaders;
use MongoDB\BSON\UTCDateTime;

/**
 * Class ApplicationInstallRepository
 *
 * @package         Hanaboso\PipesPhpSdk\Application\Repository
 *
 * @phpstan-extends DocumentRepository<ApplicationInstall>
 */
final class ApplicationInstallRepository extends DocumentRepository
{

    /**
     * @param string $key
     * @param string $user
     *
     * @return ApplicationInstall
     * @throws ApplicationInstallException
     */
    public function findUserApp(string $key, string $user): ApplicationInstall
    {
        /** @var ApplicationInstall | null $app */
        $app = $this->createQueryBuilder()
            ->field(ApplicationInstall::KEY)->equals($key)
            ->field(ApplicationInstall::USER)->equals($user)
            ->getQuery()->getSingleResult();

        if (!$app) {
            throw new ApplicationInstallException(
                sprintf('Application [%s] was not found .', $key),
                ApplicationInstallException::APP_WAS_NOT_FOUND,
            );
        }

        return $app;
    }

    /**
     * @param ProcessDto $dto
     * @param bool       $clear
     *
     * @return ApplicationInstall
     * @throws ApplicationInstallException
     */
    public function findUserAppByHeaders(ProcessDto $dto, bool $clear = TRUE): ApplicationInstall
    {
        if ($clear) {
            $this->clear();
        }

        return $this->findUserApp(
            (string) PipesHeaders::get(PipesHeaders::APPLICATION, $dto->getHeaders()),
            (string) PipesHeaders::get(PipesHeaders::USER, $dto->getHeaders()),
        );
    }

    /**
     * @return mixed[]
     * @throws DateTimeException
     */
    public function getApplicationsCount(): array
    {
        $ab  = $this->createAggregationBuilder();
        $res = $ab
            ->match()->field('deleted')->equals(FALSE)
            ->group()
            ->field('id')->expression('$key')
            ->field('total_sum')->sum(1)
            ->field('non_expire_sum')->sum(
                $ab->expr()->cond(
                    $ab->expr()->addOr(
                        $ab->expr()->gte('$expires', new UTCDateTime(DateTimeUtils::getUtcDateTime())),
                        $ab->expr()->eq('$expires', NULL),
                    ),
                    1,
                    0,
                ),
            )
            ->sort('id', 'ASC')
            ->execute()
            ->toArray();

        $ret = [];
        foreach ($res as $item) {
            $ret[] = [
                '_id'   => $item['_id'],
                'value' => ['total_sum' => $item['total_sum'], 'non_expire_sum' => $item['non_expire_sum']],
            ];
        }

        return $ret;
    }

    /**
     * @param string $application
     *
     * @return mixed[]
     * @throws DateTimeException
     */
    public function getApplicationsCountDetails(string $application): array
    {
        $ab  = $this->createAggregationBuilder();
        $res = $ab
            ->match()->field('key')->equals($application)
            ->group()->field('id')
            ->expression(
                $ab->expr()
                    ->field('active')->expression(
                        $ab->expr()->addOr(
                            $ab->expr()->gte('$expires', new UTCDateTime(DateTimeUtils::getUtcDateTime())),
                            $ab->expr()->eq('$expires', NULL),
                        ),
                    )
                    ->field('name')->ifNull('$user', ''),
            )
            ->sort('id', 'ASC')
            ->execute()
            ->toArray();

        $ret = ['_id' => $application];
        foreach ($res as $item) {
            $ret['value']['users'][] = ['active' => $item['_id']['active'], 'name' => $item['_id']['name']];
        }

        return [$ret];
    }

}
