<?php declare(strict_types=1);

namespace Hanaboso\PipesPhpSdk\HbPFTableParserBundle\Handler;

use Doctrine\ODM\MongoDB\LockException;
use Doctrine\ODM\MongoDB\Mapping\MappingException;
use Hanaboso\CommonsBundle\Exception\FileStorageException;
use Hanaboso\CommonsBundle\FileStorage\FileStorage;
use Hanaboso\PipesPhpSdk\Parser\Exception\TableParserException;
use Hanaboso\PipesPhpSdk\Parser\TableParser;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Exception as WriterException;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class TableParserHandler
 *
 * @package Hanaboso\PipesPhpSdk\HbPFTableParserBundle\Handler
 */
class TableParserHandler
{

    /**
     * @var TableParser
     */
    private $tableParser;

    /**
     * @var FileStorage
     */
    private $fileStorage;

    /**
     * TableParserHandler constructor.
     *
     * @param TableParser $tableParser
     * @param FileStorage $fileStorage
     */
    public function __construct(TableParser $tableParser, FileStorage $fileStorage)
    {
        $this->tableParser = $tableParser;
        $this->fileStorage = $fileStorage;
    }

    /**
     * @param mixed[] $data
     *
     * @return string
     * @throws TableParserHandlerException
     * @throws Exception
     * @throws ReaderException
     * @throws FileStorageException
     */
    public function parseToJson(array $data): string
    {
        $fs    = new Filesystem();
        $isTmp = FALSE;
        $path  = $this->getFile($data, $isTmp, $fs);
        $res   = $this->tableParser->parseToJson($path, $data['has_headers'] ?? FALSE);

        if ($isTmp) {
            $fs->remove($path);
        }

        return $res;
    }

    /**
     */
    public function parseToJsonTest(): bool
    {
        return TRUE;
    }

    /**
     * @param string  $type
     * @param mixed[] $data
     *
     * @return string
     * @throws Exception
     * @throws TableParserException
     * @throws TableParserHandlerException
     * @throws WriterException
     * @throws FileStorageException
     */
    public function parseFromJson(string $type, array $data): string
    {
        $fs    = new Filesystem();
        $isTmp = FALSE;
        $path  = $this->getFile($data, $isTmp, $fs);
        $res   = $this->tableParser->parseFromJson($path, $type, $data['has_headers'] ?? FALSE);

        if ($isTmp) {
            $fs->remove($path);
        }

        return $res;
    }

    /**
     * @param string $type
     *
     * @return bool
     * @throws TableParserException
     */
    public function parseFromJsonTest(string $type): bool
    {
        $this->tableParser->createWriter(new Spreadsheet(), $type);

        return TRUE;
    }

    /**
     * @param mixed[]         $data
     * @param bool            $isTmp
     * @param Filesystem|null $fs
     *
     * @return string
     * @throws FileStorageException
     * @throws TableParserHandlerException
     * @throws LockException
     * @throws MappingException
     */
    private function getFile(array $data, bool &$isTmp, ?Filesystem $fs = NULL): string
    {
        if (isset($data['file_id'])) {
            if (!$fs) {
                $fs = new Filesystem();
            }

            if ($fs->exists($data['file_id'])) {
                $isTmp = FALSE;

                return $data['file_id'];
            }

            $isTmp = TRUE;
            $file  = $this->fileStorage->getFileDocument($data['file_id']);
            $file  = $this->fileStorage->getFileStorage($file)->getContent();
            $path  = sprintf('%s/%s', __DIR__, uniqid());
            $fs->dumpFile($path, $file);

            return $path;
        }

        throw new TableParserHandlerException(
            'Property not set: \'file_id\'',
            TableParserHandlerException::PROPERTY_FILE_ID_NOT_SET
        );
    }

}
