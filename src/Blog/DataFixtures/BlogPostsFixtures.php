<?php

namespace App\Blog\DataFixtures;

use App\Blog\Entity\BlogCategory;
use App\Blog\Entity\BlogPost;
use App\Blog\Entity\BlogTag;
use App\Core\DataFixtures\AbstractFixture;
use App\Core\Enum\Locale;
use App\Core\Util\Arrays;
use App\Core\Util\Files;
use App\Core\Util\HtmlGenerator;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as Faker;
use Gaufrette\Filesystem;
use Knp\Bundle\GaufretteBundle\FilesystemMap;
use Nette\Utils\Json;
use Nette\Utils\JsonException;
use ProseMirror\ProseMirror;

class BlogPostsFixtures extends AbstractFixture implements DependentFixtureInterface
{
    private array $categories = [];
    private array $tags = [];
    private array $images = [];
    private Filesystem $filesystem;

    public function __construct(string $env, FilesystemMap $filesystemMap)
    {
        parent::__construct($env);
        $this->filesystem = $filesystemMap->get('blog_post_image_fs');
    }

    public function load(ObjectManager $manager): void
    {
        // fetch categories
        foreach ($manager->getRepository(BlogCategory::class)->findAll() as $category) {
            $locales = $category->getLocales();
            if (empty($locales)) {
                continue;
            }
            $locale = substr($locales[0], 0, 2);
            if (false === isset($this->categories[$locale])) {
                $this->categories[$locale] = [];
            }
            $this->categories[$locale][$category->getSlug()] = $category;
        }

        // fetch tags
        foreach ($manager->getRepository(BlogTag::class)->findAll() as $tag) {
            $this->tags[$tag->getSlug()] = $tag;
        }

        // fetch images
        $this->images = [
            '1' => __DIR__ . '/files/post-image-1.jpg',
            '2' => __DIR__ . '/files/post-image-2.jpg',
            '3' => __DIR__ . '/files/post-image-3.jpg',
            '4' => __DIR__ . '/files/post-image-4.jpg',
            '5' => __DIR__ . '/files/post-image-5.jpg',
        ];

        // process data
        foreach ($this->getData() as $d) {
            try {
                $contentJson = Json::encode(ProseMirror::htmlToJson($d['contentHtml']));
            } catch (JsonException $exception) {
                continue;
            }

            $post = (new BlogPost())
                ->setTitle($d['title'])
                ->setExcerpt($d['excerpt'])
                ->setMetaTitle($d['metaTitle'])
                ->setMetaDescription($d['metaDescription'])
                ->setCategory($d['category'])
                ->setImageAlt($d['imageAlt'])
                ->setContentHtml($d['contentHtml'])
                ->setContentJson($contentJson)
                ->setPublished($d['published'])
                ->setCreatedAt($d['createdAt'])
                ->setPublishedAt($d['publishedAt'])
            ;

            // locales
            foreach (($d['locales'] ?? []) as $locale) {
                $post->addLocale($locale);
            }

            // image
            if (null !== ($d['imageFile'] ?? null)) {
                $post->setImageFile(Files::getUploadedFile($d['imageFile']));
            } elseif (null !== ($d['image'] ?? null)) {
                $imagePath = $d['image']['path'] ?? null;
                if (null === $imagePath) {
                    throw new \InvalidArgumentException();
                }
                $imageBasename = $d['image']['basename'] ?? null;
                if (null === $imageBasename) {
                    throw new \InvalidArgumentException();
                }
                if (false === $imageContent = file_get_contents($imagePath)) {
                    throw new \InvalidArgumentException();
                }

                $this->filesystem->write($imageBasename, $imageContent, true);
                $post->setImage($imageBasename);
            }

            foreach ($d['tags'] as $tag) {
                $post->addTag($tag);
            }

            $manager->persist($post);

            $post->setUpdatedAt($d['updatedAt']);  // because vich call setImageFile on persist that override the updatedAt property value
        }

        $manager->flush();
    }

    public function getDevData(): array
    {
        $data = [];

        // fixed data
        $data[] = [
            'title' => 'Symfony UX Turbo: Do You Still Need JavaScript?!',
            'excerpt' => 'Hotwire Turbo is a tiny library recently introduced by DHH allowing to have the speed of Single-Page Apps without having to write any JavaScript!',
            'contentHtml' => <<<'HTML'
<h2>Hotwire Turbo</h2>
<p>Hotwire Turbo is a tiny library recently introduced by <a target="_blank" rel="noopener noreferrer nofollow" href="https://dhh.dk/">DHH</a> (the creator of Ruby on Rails) allowing to have the speed of Single-Page Apps without having to write any JavaScript!</p><p>As part of the Symfony UX initiative, I’m very excited to announce the immediate availability of Symfony UX Turbo: the official integration of Turbo in Symfony. With Symfony UX Turbo, you can get rid of JavaScript and enjoy using Twig again!</p>

<h2>About implementation</h2>
<pre><code>&lt;?php
// src/Controller/PagesController.php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class PagesController extends AbstractController
{
    #[Route('/', name: 'app_homepage')]
    public function home(): Response
    {
        return $this-&gt;render('pages/home.html.twig');
    }

    #[Route('/produits', name: 'app_produits')]
    public function products(): Response
    {
        $products = [];

        for ($i = 1; $i &lt;= 5; $i++) {
            $products[] = [
                'title' =&gt; "Produit $i",
            ];
        }

        return $this-&gt;render('pages/produits/list.html.twig', [
            'products' =&gt; $products,
        ]);
    }
}</code></pre>
<p>In this slide deck, we’ll discover how the library works, how to leverage it to enhance your Twig templates and how to add real-time features to your websites with the native support of the Mercure protocol!</p>
<blockquote><p>Testing is doubting</p></blockquote>
<p>In this slide deck, we’ll discover how the library works, how to leverage it to enhance your Twig templates and how to add real-time features to your websites with the native support of the Mercure protocol!</p>
HTML
            ,
            'metaTitle' => 'Symfony UX Turbo: Do You Still Need JavaScript?!',
            'metaDescription' => 'Hotwire Turbo is a tiny library recently introduced by DHH allowing to have the speed of Single-Page Apps without having to write any JavaScript!',
            'category' => $this->categories['fr']['actualites-tech'],
            'tags' => [$this->tags['developpeur'], $this->tags['code'], $this->tags['web']],
            'imageFile' => __DIR__ . '/files/post-image-symfony-turbo.png',
            'imageAlt' => 'Symfony UX Turbo: Do You Still Need JavaScript?!',
            'published' => true,
            'createdAt' => new \DateTime('yesterday'),
            'publishedAt' => new \DateTime('today'),
            'updatedAt' => new \DateTime('now'),
            'locales' => [Locale::fr_FR],
        ];

        $locales = [
            'fr' => [
                Locale::fr_FR,
                Locale::fr_BE,
                Locale::fr_CH,
                Locale::fr_LU,
            ],
            'en' => [
                Locale::en_GB,
            ],
        ];

        $fakers = [
            'fr' => Faker::create('fr_FR'),
            'en' => Faker::create('en_US'),
        ];

        // random data
        $postsCount = 60;
        for ($i = 0; $i < $postsCount; ++$i) {
            if ($i < 25) {
                $locale = 'fr';
                $bpLocales = [Locale::fr_FR];
                $bpCategory = $this->categories[$locale]['actualites-tech'];
            } else {
                $locale = Arrays::getRandom(array_keys($locales));
                $bpLocales = Arrays::getRandomSubarray($locales[$locale], 1);
                $bpCategory = Arrays::getRandom($this->categories[$locale]);
            }

            $faker = $fakers[$locale];
            $createdAt = $faker->dateTimeBetween('- 12 months');
            $publishedAt = $faker->dateTimeBetween($createdAt, '+ 1 months');
            $updatedAt = (0 === random_int(0, 1) || $publishedAt > new \DateTime('now')) ? clone $publishedAt : $faker->dateTimeBetween($publishedAt);

            $data[] = [
                'title' => ucfirst($faker->realText(32)),
                'excerpt' => ucfirst($faker->realText(150)),
                'contentHtml' => HtmlGenerator::generate(),
                'metaTitle' => ucfirst($faker->realText(56)),
                'metaDescription' => ucfirst($faker->realText(150)),
                'tags' => Arrays::getRandomSubarray($this->tags, 1, 3),
                'imageFile' => Arrays::getRandom($this->images),
                'imageAlt' => null,
                'published' => 0 !== random_int(0, 3),
                'createdAt' => $createdAt,
                'publishedAt' => $publishedAt,
                'updatedAt' => $updatedAt,
                'locales' => $bpLocales,
                'category' => $bpCategory,
            ];
        }

        return $data;
    }

    public function getTestData(): array
    {
        return [
            [
                'title' => 'Blog - Category 1 - Post 1',
                'excerpt' => 'Blog - Category 1 - Post 1 // Excerpt',
                'contentHtml' => '<p>Blog - Category 1 - Post 1 // Content</p>',
                'metaTitle' => 'Blog - Category 1 - Post 1 // Meta title',
                'metaDescription' => 'Blog - Category 1 - Post 1 // Meta description',
                'category' => $this->categories['fr']['blog-category-1'],
                'tags' => [
                    $this->tags['blog-tag-1'],
                    $this->tags['blog-tag-2'],
                    $this->tags['blog-tag-3'],
                ],
                'image' => [
                    'path' => $this->images['1'],
                    'basename' => 'cat1art1-image.jpg',
                ],
                'imageAlt' => null,
                'locales' => [
                    Locale::fr_FR,
                    Locale::fr_BE,
                    Locale::fr_CH,
                    Locale::fr_LU,
                ],
                'published' => true,
                'createdAt' => new \DateTime('2021-01-01 10:00:00'),
                'publishedAt' => new \DateTime('2021-01-01 20:00:00'),
                'updatedAt' => new \DateTime('2021-01-01 22:00:00'),
            ],
            [
                'title' => 'Blog - Category 1 - Post 2',
                'excerpt' => 'Blog - Category 1 - Post 2 // Excerpt',
                'contentHtml' => '<p>Blog - Category 1 - Post 2 // Content</p>',
                'metaTitle' => 'Blog - Category 1 - Post 2 // Meta title',
                'metaDescription' => 'Blog - Category 1 - Post 2 // Meta description',
                'category' => $this->categories['fr']['blog-category-1'],
                'tags' => [
                    $this->tags['blog-tag-1'],
                    $this->tags['blog-tag-2'],
                ],
                'image' => [
                    'path' => $this->images['2'],
                    'basename' => 'cat1art2-image.jpg',
                ],
                'imageAlt' => 'Blog - Category 1 - Post 2 // Image alt',
                'locales' => [
                    Locale::fr_FR,
                    Locale::fr_BE,
                    Locale::fr_CH,
                    Locale::fr_LU,
                ],
                'published' => true,
                'createdAt' => new \DateTime('2021-01-01 11:00:00'),
                'publishedAt' => new \DateTime('2021-01-01 11:00:00'),
                'updatedAt' => new \DateTime('2021-01-01 11:00:00'),
            ],
            [
                'title' => 'Blog - Category 1 - Post 3 - Lorem',
                'excerpt' => 'Blog - Category 1 - Post 3 // Excerpt',
                'contentHtml' => '<p>Blog - Category 1 - Post 3 // Content</p>',
                'metaTitle' => 'Blog - Category 1 - Post 3 // Meta title',
                'metaDescription' => 'Blog - Category 1 - Post 3 // Meta description',
                'category' => $this->categories['fr']['blog-category-1'],
                'tags' => [
                    $this->tags['blog-tag-1'],
                    $this->tags['blog-tag-2'],
                ],
                'image' => [
                    'path' => $this->images['3'],
                    'basename' => 'cat1art3-image.jpg',
                ],
                'imageAlt' => 'Blog - Category 1 - Post 3 // Image alt',
                'locales' => [
                    Locale::fr_FR,
                    Locale::fr_BE,
                    Locale::fr_CH,
                    Locale::fr_LU,
                ],
                'published' => true,
                'createdAt' => new \DateTime('2021-01-01 12:00:00'),
                'publishedAt' => new \DateTime('2021-01-01 12:00:00'),
                'updatedAt' => new \DateTime('2021-01-01 12:00:00'),
            ],
            [
                'title' => 'Blog - Category 1 - Post 4',
                'excerpt' => 'Blog - Category 1 - Post 4 // Excerpt',
                'contentHtml' => '<p>Blog - Category 1 - Post 4 // Content</p>',
                'metaTitle' => 'Blog - Category 1 - Post 4 // Meta title',
                'metaDescription' => 'Blog - Category 1 - Post 4 // Meta description',
                'category' => $this->categories['fr']['blog-category-1'],
                'tags' => [
                    $this->tags['blog-tag-1'],
                ],
                'image' => [
                    'path' => $this->images['4'],
                    'basename' => 'cat1art4-image.jpg',
                ],
                'imageAlt' => 'Blog - Category 1 - Post 4 // Image alt',
                'locales' => [
                    Locale::fr_FR,
                    Locale::fr_BE,
                    Locale::fr_CH,
                    Locale::fr_LU,
                ],
                'published' => true,
                'createdAt' => new \DateTime('2021-01-01 13:00:00'),
                'publishedAt' => new \DateTime('2021-01-01 13:00:00'),
                'updatedAt' => new \DateTime('2021-01-01 13:00:00'),
            ],
            [
                'title' => 'Blog - Category 2 - Post 1',
                'excerpt' => 'Blog - Category 1 - Post 1 // Excerpt',
                'contentHtml' => '<p>Blog - Category 2 - Post 1 // Content - Lorem</p>',
                'metaTitle' => 'Blog - Category 2 - Post 1 // Meta title',
                'metaDescription' => 'Blog - Category 2 - Post 1 // Meta description',
                'category' => $this->categories['fr']['blog-category-2'],
                'tags' => [
                    $this->tags['blog-tag-1'],
                ],
                'image' => [
                    'path' => $this->images['5'],
                    'basename' => 'cat2art1-image.jpg',
                ],
                'imageAlt' => 'Blog - Category 2 - Post 1 // Image alt',
                'published' => true,
                'createdAt' => new \DateTime('2021-01-01 12:00:00'),
                'publishedAt' => new \DateTime('2021-01-01 12:00:00'),
                'updatedAt' => new \DateTime('2021-01-01 12:00:00'),
            ],
            [
                'title' => 'Blog - Category 3 - Post 1',
                'excerpt' => 'Blog - Category 3 - Post 1 // Excerpt',
                'contentHtml' => '<p>Blog - Category 3 - Post 1 // Content - Lorem</p>',
                'metaTitle' => 'Blog - Category 3 - Post 1 // Meta title',
                'metaDescription' => 'Blog - Category 3 - Post 1 // Meta description',
                'category' => $this->categories['fr']['blog-category-3'],
                'tags' => [
                    $this->tags['blog-tag-1'],
                    $this->tags['blog-tag-4'],
                ],
                'image' => [
                    'path' => $this->images['1'],
                    'basename' => 'cat3art1-image.jpg',
                ],
                'imageAlt' => 'Blog - Category 3 - Post 1 // Image alt',
                'locales' => [
                    Locale::fr_BE,
                    Locale::nl_BE,
                ],
                'published' => true,
                'createdAt' => new \DateTime('2021-01-01 11:00:00'),
                'publishedAt' => new \DateTime('2021-01-01 11:00:00'),
                'updatedAt' => new \DateTime('2021-01-01 11:00:00'),
            ],
            [
                'title' => 'Blog - Category 3 - Post 2 - Not published',
                'excerpt' => 'Blog - Category 3 - Post 2 // Excerpt',
                'contentHtml' => '<p>Blog - Category 3 - Post 2 // Content - Lorem</p>',
                'metaTitle' => 'Blog - Category 3 - Post 2 // Meta title',
                'metaDescription' => 'Blog - Category 3 - Post 2 // Meta description',
                'category' => $this->categories['fr']['blog-category-3'],
                'tags' => [
                    $this->tags['blog-tag-1'],
                    $this->tags['blog-tag-4'],
                ],
                'image' => [
                    'path' => $this->images['2'],
                    'basename' => 'cat3art2-image.jpg',
                ],
                'imageAlt' => 'Blog - Category 3 - Post 2 // Image alt',
                'locales' => [
                    Locale::fr_BE,
                    Locale::nl_BE,
                ],
                'published' => false,
                'createdAt' => new \DateTime('2021-01-01 11:00:00'),
                'publishedAt' => new \DateTime('2021-01-01 11:00:00'),
                'updatedAt' => new \DateTime('2021-01-01 11:00:00'),
            ],
            [
                'title' => 'Blog - Category 3 - Post 3 - Published in the future',
                'excerpt' => 'Blog - Category 3 - Post 3 // Excerpt',
                'contentHtml' => '<p>Blog - Category 3 - Post 3 // Content - Lorem</p>',
                'metaTitle' => 'Blog - Category 3 - Post 3 // Meta title',
                'metaDescription' => 'Blog - Category 3 - Post 3 // Meta description',
                'category' => $this->categories['fr']['blog-category-3'],
                'tags' => [
                    $this->tags['blog-tag-1'],
                    $this->tags['blog-tag-4'],
                ],
                'image' => [
                    'path' => $this->images['3'],
                    'basename' => 'cat3art3-image.jpg',
                ],
                'imageAlt' => 'Blog - Category 3 - Post 3 // Image alt',
                'locales' => [
                    Locale::fr_BE,
                    Locale::nl_BE,
                ],
                'published' => true,
                'createdAt' => new \DateTime('2021-01-01 11:00:00'),
                'publishedAt' => new \DateTime('tomorrow'),
                'updatedAt' => new \DateTime('2021-01-01 11:00:00'),
            ],
        ];
    }

    public function getDependencies()
    {
        return [
            BlogCategoriesFixtures::class,
            BlogTagsFixtures::class,
        ];
    }
}
