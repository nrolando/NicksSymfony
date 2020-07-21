<?php

namespace App\Services;

use App\Repository\PostRepository;
use Psr\Container\ContainerInterface;
use Symfony\Component\Form\Exception\RuntimeException as SymfonyFormRuntimeException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Zend\Code\Exception\RuntimeException as ZendRuntimeException;

class FileUploader
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function uploadFile(UploadedFile $file) {
        /** testFileSize() will not be needed because if size is larger than php_ini upload_max_filesize then
         * $file->getError() !== 0
         */
        if($file->getError() !== 0) {
            throw new SymfonyFormRuntimeException("There was an error uploading your file.");
        }

        if($file->getSize() === false || !$this->testFileSize($file->getSize())) {
            throw new SymfonyFormRuntimeException("Filesize is too large.");
        }

        $ext = $file->guessExtension();
        if(is_null($ext)) {
            $ext = $file->guessClientExtension();
            // Using $file->getExtension() could be unsafe - https://symfony.com/doc/current/controller/upload_file.html
            if(is_null($ext)) {
                return null;
            }
        }

        $filename = md5(uniqid()) . '.' . $ext;

        $file->move(
            $this->container->getParameter('uploads_dir'),
            $filename
        );

        return $filename;
    }

    private function testFileSize($filesize) :bool {
        $phpIniMaxFileSize = ini_get('upload_max_filesize');
        $res = preg_match('/[kKmMgG]/', $phpIniMaxFileSize, $matches);
        if($res === 1) {
            $unit = strtoupper($matches[0]);
            $res = preg_match('/[0-9]+/', $phpIniMaxFileSize, $matches);
            $phpIniMaxFileSize = intval($matches[0]);
            switch($unit) {
                case "K":
                    $phpIniMaxFileSize *= 1024;
                    break;
                case "M":
                    $phpIniMaxFileSize *=  1048576;
                    break;
                case "G":
                    $phpIniMaxFileSize *= 1073741824;
                    break;
                default:
                    throw new ZendRuntimeException("Error in request, please consult web admin.");
            }
        } elseif ($res === 0) {
            $phpIniMaxFileSize = intval($phpIniMaxFileSize);
        } else {
            throw new ZendRuntimeException("Error in request, please consult web admin.");
        }

        if($phpIniMaxFileSize > $filesize) {
            return true;
        } else {
            return false;
        }
    }
}
