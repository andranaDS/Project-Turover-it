<?php

namespace App\Core\Naming;

use App\Core\Util\TokenGenerator;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\Naming\ConfigurableInterface;
use Vich\UploaderBundle\Naming\NamerInterface;

class FileNamer implements NamerInterface, ConfigurableInterface
{
    private array $options;

    public function configure(array $options): void
    {
        $this->options = $options;
    }

    public function name($object, PropertyMapping $mapping): string
    {
        /* @var $file UploadedFile */
        $file = $mapping->getFile($object);

        if (null === $file) {
            throw new \InvalidArgumentException('File is null');
        }

        $name = $file->getClientOriginalName();
        $token = TokenGenerator::generate();

        if (isset($this->options['filename']) && null !== $extension = pathinfo($name)['extension'] ?? null) {
            $name = $this->options['filename'] . '-' . $token . '.' . $extension;
        } else {
            $name .= '-' . $token;
        }

        return $name;
    }
}
