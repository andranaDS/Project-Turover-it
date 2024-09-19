<?php

namespace App\Core\Entity;

use Symfony\Component\Serializer\Annotation\Groups;

class SkillJob
{
    public const TYPE_SKILL = 'skill';
    public const TYPE_JOB = 'job';

    /**
     * @Groups({"core:autocomplete:jobs_skills"})
     */
    private string $name;

    /**
     * @Groups({"core:autocomplete:jobs_skills"})
     */
    private string $slug;

    /**
     * @Groups({"core:autocomplete:jobs_skills"})
     */
    private string $type;

    public function __construct(string $name, string $slug, string $type)
    {
        $this->name = $name;
        $this->slug = $slug;
        $this->type = $type;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
