<?php

namespace App\Core\Cache;

use App\Core\Annotation\ApiEnum;
use App\Core\Annotation\ApiFileUrl;
use App\Core\Annotation\ApiThumbnailUrls;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Adapter\PhpArrayAdapter;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class AnnotationsCache
{
    private string $projectDir;
    private string $projectEnv;

    public function __construct(string $projectDir, string $env)
    {
        $this->projectDir = $projectDir;
        $this->projectEnv = $env;
    }

    public function getAnnotations(?bool $reset = false): array
    {
        $cache = new PhpArrayAdapter($this->projectDir . '/var/cache/' . $this->projectEnv . '/app/annotations.php', new FilesystemAdapter());

        $annotations = $cache->getItem('annotations')->get();

        if (true === $reset || null === $annotations) {
            $finder = new Finder();
            $annotationReader = new AnnotationReader();

            $annotations = [];

            foreach ($finder->in($this->projectDir . '/src/*/Entity')->name('*.php') as $file) {
                /** @var SplFileInfo $file */
                $class = str_replace(
                    [$this->projectDir . '/src', '.php', '/'], ['App', '', '\\'],
                    $file->getPathname()
                );

                if (false === class_exists($class)) {
                    throw new \RuntimeException(sprintf('"%s" must be a valid class name', $class));
                }

                $reflectionClass = new \ReflectionClass($class);
                foreach ($reflectionClass->getProperties() as $reflectionProperty) {
                    if (null !== ($propertyAnnotation = $annotationReader->getPropertyAnnotation($reflectionProperty, ApiEnum::class))) {
                        $annotations[$class][ApiEnum::class][$reflectionProperty->getName()] = [
                            'class' => $propertyAnnotation->getClass(),
                            'translationDomain' => $propertyAnnotation->getTranslationDomain(),
                        ];
                    } elseif (null !== ($propertyAnnotation = $annotationReader->getPropertyAnnotation($reflectionProperty, ApiThumbnailUrls::class))) {
                        $annotations[$class][ApiThumbnailUrls::class][$reflectionProperty->getName()] = [
                            'filters' => $propertyAnnotation->getFilters(),
                        ];
                    } elseif (null !== ($propertyAnnotation = $annotationReader->getPropertyAnnotation($reflectionProperty, ApiFileUrl::class))) {
                        $annotations[$class][ApiFileUrl::class][$reflectionProperty->getName()] = [
                            'property' => $propertyAnnotation->getProperty(),
                        ];
                    }
                }
            }

            $cache->warmUp([
                'annotations' => $annotations,
            ]);
        }

        return $annotations;
    }
}
