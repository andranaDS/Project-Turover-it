<?php

namespace App\JobPosting\Contracts;

use App\Company\Entity\Company;

interface JobPostingInterface
{
    public function getCompany(): ?Company;
}
