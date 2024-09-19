<?php

namespace App\Core\Naming;

use App\Core\Util\TokenGenerator;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Vich\UploaderBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\Naming\ConfigurableInterface;
use Vich\UploaderBundle\Naming\NamerInterface;

class SlugNamer implements NamerInterface, ConfigurableInterface
{
    private array $options;
    private PropertyAccessorInterface $propertyAccessor;
    private SluggerInterface $slugger;

    public function __construct(PropertyAccessorInterface $propertyAccessor, SluggerInterface $slugger)
    {
        $this->propertyAccessor = $propertyAccessor;
        $this->slugger = $slugger;
    }

    public function configure(array $options): void
    {
        $this->options = $options;
    }

    public function name($object, PropertyMapping $mapping): string
    {
        if (!isset($this->options['property'])) {
            throw new \InvalidArgumentException('Options "property" is required.');
        }

        if (!\is_string($this->options['property'])) {
            throw new \InvalidArgumentException('Options "property" must be type of string.');
        }

        /* @var $file UploadedFile */
        $file = $mapping->getFile($object);
        if (null === $file) {
            throw new \InvalidArgumentException('File is null');
        }

        $name = mb_strtolower($this->slugger->slug($this->propertyAccessor->getValue($object, $this->options['property'])), 'utf8') . '-' . TokenGenerator::generate();
        if (null !== $extension = pathinfo($file->getClientOriginalName())['extension'] ?? null) {
            $name .= '.' . $extension;
        }

        return $name;
    }
}
