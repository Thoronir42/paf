<?php

namespace App\Common\Services\Storage;


use App\Common\Model\Entity\Quote;
use Nette\Http\FileUpload;
use SeStep\FileAttachable\Model\FileEntity;
use SeStep\FileAttachable\Service\Files;

class PafImageStorage
{
    /** @var FileStorage */
    private $fileStorage;
    /** @var Files */
    private $files;

    public function __construct(FileStorage $fileStorage, Files $files)
    {
        $this->fileStorage = $fileStorage;
        $this->files = $files;
    }

    /**
     * @param Quote      $quote
     * @param FileUpload $file
     * @param            $name
     * @return FileEntity
     */
    public function saveQuoteReference(Quote $quote, FileUpload $file, $name)
    {
        if (!$file->isImage()) {
            return null;
        }
        
        $fileName = $this->fileStorage->save("quotes" . DIRECTORY_SEPARATOR . $name, $file);

        $fileEntity = new FileEntity($fileName, $file->getSize());
        $this->files->save($fileEntity, false);

        return $fileEntity;
    }
}
