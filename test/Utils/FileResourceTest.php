<?php declare(strict_types=1);

namespace PAF\Utils;

trait FileResourceTest
{

    protected function createFileUpload(string $tmpName, string $name = null): \Nette\Http\FileUpload
    {
        $testResourceDir = dirname(__DIR__) . '/resources';
        $path = "$testResourceDir/$tmpName";
        if (!file_exists($path)) {
            throw new \InvalidArgumentException("File '$tmpName' not found in '$testResourceDir'");
        }
        if (!$name) {
            $name = $tmpName;
        }

        return new \Nette\Http\FileUpload([
            'name' => $name ?: $tmpName,
            'size' => filesize($path),
            'tmp_name' => $path,
            'error' => UPLOAD_ERR_OK,
        ]);
    }
}
