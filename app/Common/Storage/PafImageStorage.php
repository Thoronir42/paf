<?php declare(strict_types=1);

namespace PAF\Common\Storage;

use Nette\Http\FileUpload;
use PAF\Modules\CommissionModule\Model\Quote;
use PAF\Modules\CommonModule\Model\UserFile;
use PAF\Modules\CommonModule\Model\UserFileThread;
use PAF\Modules\CommonModule\Services\FilesService;

// todo: implement properly
class PafImageStorage
{
    /** @var FileStorage */
    private $fileStorage;
    /** @var FilesService */
    private $files;

    public function __construct(FileStorage $fileStorage, FilesService $files)
    {
        $this->fileStorage = $fileStorage;
        $this->files = $files;
    }

    /**
     * @param Quote $quote
     * @param FileUpload[] $references
     */
    public function setQuoteReferences(Quote $quote, array $references)
    {
        if (!$quote->hasReferences()) {
            $quote->references = $this->files->createThread(true);
        }

        foreach ($references as $file) {
            $this->saveImageFile('quote', $file, $quote->slug->id, $quote->references);
        }
    }

    public function saveImageFile(
        string $category,
        FileUpload $file,
        string $name,
        UserFileThread $thread = null
    ): ?UserFile {
        if (!$file->isImage()) {
            return null;
        }

        $fileName = $this->fileStorage->save($name, $file, $category);

        $fileEntity = new UserFile();
        $fileEntity->filename = $fileName;
        $fileEntity->size = $file->getSize();
        $fileEntity->thread = $thread;

        $this->files->save($fileEntity);

        return $fileEntity;
    }
}
