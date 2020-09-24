<?php declare(strict_types=1);

namespace Hanaboso\PipesPhpSdk\Application\Listener;

use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Doctrine\ODM\MongoDB\Event\PreFlushEventArgs;
use Hanaboso\CommonsBundle\Crypt\CryptManager;
use Hanaboso\CommonsBundle\Crypt\Exceptions\CryptException;
use Hanaboso\PipesPhpSdk\Application\Document\ApplicationInstall;

/**
 * Class ApplicationInstallListener
 *
 * @package Hanaboso\PipesPhpSdk\Application\Listener
 */
final class ApplicationInstallListener
{

    /**
     * @var CryptManager
     */
    private CryptManager $cryptManager;

    /**
     * ApplicationInstallListener constructor.
     *
     * @param CryptManager $cryptManager
     */
    public function __construct(CryptManager $cryptManager)
    {
        $this->cryptManager = $cryptManager;
    }

    /**
     * @param LifecycleEventArgs $args
     *
     * @throws CryptException
     */
    public function postLoad(LifecycleEventArgs $args): void
    {
        $document = $args->getDocument();

        if ($document instanceof ApplicationInstall) {
            $document->setSettings(
                !empty($document->getEncryptedSettings()) ?
                    $this->cryptManager->decrypt($document->getEncryptedSettings()) : []
            );
        }
    }

    /**
     * @param PreFlushEventArgs $args
     *
     * @throws CryptException
     */
    public function preFlush(PreFlushEventArgs $args): void
    {
        $uof = $args->getDocumentManager()->getUnitOfWork();
        $uof->computeChangeSets();
        $this->processDocuments($uof->getScheduledDocumentInsertions());
        $this->processDocuments($uof->getScheduledDocumentUpdates());
        $this->processDocuments($uof->getScheduledDocumentUpserts());
    }

    /**
     * @param mixed[] $documents
     *
     * @throws CryptException
     */
    private function processDocuments(array $documents): void
    {
        foreach ($documents as $document) {
            if ($document instanceof ApplicationInstall) {
                $document
                    ->setEncryptedSettings($this->cryptManager->encrypt($document->getSettings()))
                    ->setSettings([]);
            }
        }
    }

}
