<?php

namespace App\Blog\Entity;

use App\Blog\Repository\BlogPostImageRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=BlogPostImageRepository::class)
 * @Vich\Uploadable()
 */
class BlogPostImage
{
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @Vich\UploadableField(mapping="blog_post_image_file", fileNameProperty="image")
     * @Assert\Image(
     *     maxSizeMessage="generic.file.max_size",
     *     minWidthMessage="generic.file.image.min_width",
     *     minHeightMessage="generic.file.image.min_height",
     *     maxWidth="generic.file.image.max_width",
     *     maxHeight="generic.file.image.max_height",
     *     mimeTypesMessage="generic.file.mime_type",
     *     maxSize="5M",
     *     minWidth=500,
     *     minHeight=500,
     *     maxWidth=2048,
     *     maxHeight=2048,
     *     mimeTypes={"image/jpeg","image/png","image/gif", "image/jpg"}
     * )
     */
    private ?File $imageFile = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $image = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageFile(?File $imageFile): self
    {
        $this->imageFile = $imageFile;

        if ($imageFile) {
            $this->updatedAt = new \DateTime('now');
        }

        return $this;
    }
}
