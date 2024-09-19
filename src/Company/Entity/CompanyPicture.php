<?php

namespace App\Company\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Company\Repository\CompanyPictureRepository;
use App\Core\Annotation\ApiThumbnailUrls;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=CompanyPictureRepository::class)
 * @Vich\Uploadable()
 * @ApiResource(
 *     itemOperations={
 *          "get"={
 *              "method"="GET",
 *              "normalization_context"={"groups"={"company_picture:get"}},
 *          },
 *      },
 *     collectionOperations={},
 * )
 */
class CompanyPicture
{
    use SoftDeleteableEntity;
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"company:get", "company_picture:get", "company:post:directory_media"})
     */
    private ?int $id = null;

    /**
     * @Vich\UploadableField(mapping="company_picture_image", fileNameProperty="image")
     * @Assert\Image(
     *     maxSizeMessage="generic.file.max_size",
     *     minWidthMessage="generic.file.image.min_width",
     *     minHeightMessage="generic.file.image.min_height",
     *     maxWidth="generic.file.image.max_width",
     *     maxHeight="generic.file.image.max_height",
     *     mimeTypesMessage="generic.file.mime_type",
     *     maxSize="30M",
     *     minWidth=500,
     *     minHeight=500,
     *     maxWidth=4096,
     *     maxHeight=4096,
     *     mimeTypes={"image/jpeg","image/png","image/gif", "image/jpg"},
     *     groups={"company:post:directory_media"}
     * )
     */
    private ?File $imageFile = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Groups({"company:get", "company_picture:get", "job_posting:get", "company:get:homepage", "company:post:directory_media"})
     * @ApiThumbnailUrls({
     *     { "name"="medium", "filter"="company_picture_image_medium" },
     *     { "name"="large", "filter"="company_picture_image_large" },
     * })
     */
    private ?string $image = null;

    /**
     * @ORM\ManyToOne(targetEntity=Company::class, inversedBy="pictures")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private ?Company $company;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $position = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
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

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = $company;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }
}
