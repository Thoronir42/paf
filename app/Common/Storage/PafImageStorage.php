<?php declare(strict_types=1);

namespace PAF\Common\Storage;


use Nette\Http\FileUpload;
use PAF\Modules\QuoteModule\Model\Quote;
use SeStep\FileAttachable\Files;
use SeStep\FileAttachable\Model\UserFile;

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

    public function saveQuoteReference(Quote $quote, FileUpload $file, string $name): UserFile
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
