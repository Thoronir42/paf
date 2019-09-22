<?php declare(strict_types=1);

namespace PAF\Common\Storage;

use Nette\Http\FileUpload;

class FileStorage
{

    private $directory;


    public function __construct($directory)
    {
        if (substr($directory, strlen($directory) - 1, 1) !== DIRECTORY_SEPARATOR) {
            $directory .= DIRECTORY_SEPARATOR;
        }

        $this->directory = $directory;
    }

    /**
     * @param string $destFileName
     * @param FileUpload $file
     * @param string|null $fileCategory
     *
     * @return string
     */
    public function save(string $destFileName, FileUpload $file, string $fileCategory = null)
    {
        if ($fileCategory) {
            $destFileName = $fileCategory . DIRECTORY_SEPARATOR . $destFileName;
        }

        $ext = pathinfo($file->getName(), PATHINFO_EXTENSION);
        $attempt = 0;
        do {
            $destFile = sprintf("%s-%02d.%s", $destFileName, ++$attempt, $ext);
        } while (file_exists($this->directory . $destFile));

        $file->move($this->directory . $destFile);

        return $destFile;
    }

    public function delete($fileName)
    {
        return unlink($this->directory . $fileName);
    }
}
