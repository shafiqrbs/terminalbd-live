<?php

namespace Core\UserBundle\Listener;

use Gedmo\Uploadable\UploadableListener as BaseListener,
    Gedmo\Uploadable\FileInfo\FileInfoInterface;

class UploadableListener extends BaseListener
{

    public function moveFile(FileInfoInterface $fileInfo, $path, $filenameGeneratorClass = false, $overwrite = false, $appendNumber = false, $object)
    {
        $info = parent::moveFile($fileInfo, $path, $filenameGeneratorClass, $overwrite, $appendNumber, $object);
        $info['fileName'] = basename($info['filePath']);

        return $info;
    }
}