<?php

namespace App\Company\Manager;

use App\Company\Entity\Company;

class CompanyQualityManager
{
    public function getQuality(Company $company): int
    {
        $quality = 0;

        if (null !== $company->getLogo()) {
            $quality += 15;
        }

        if (null !== $company->getCoverPicture()) {
            $quality += 10;
        }

        if (null !== $company->getName()) {
            $quality += 5;
        }

        if (null !== $company->getBaseline()) {
            $quality += 5;
        }

        if (null !== $company->getLocation()) {
            $quality += 2;
        }

        if (null !== $company->getCreationYear()) {
            $quality += 2;
        }

        if (null !== $company->getAnnualRevenue()) {
            $quality += 2;
        }

        if (null !== $company->getSize()) {
            $quality += 2;
        }

        $countPictures = $company->getPictures()->count();

        if (1 === $countPictures) {
            $quality += 5;
        } elseif (2 === $countPictures) {
            $quality += 10;
        } elseif ($countPictures > 2) {
            $quality += 15;
        }

        if (null !== $company->getDescription()) {
            $descLength = \strlen($company->getDescription());

            if (1 < $descLength && $descLength < 100) {
                $quality += 5;
            } elseif ($descLength > 100) {
                $quality += 30;
            }
        }

        $skillsCount = $company->getSkills()->count();
        $softSkillsCount = $company->getSoftSkills()->count();

        $quality += $this->skillsQuality($quality, $skillsCount);
        $quality += $this->skillsQuality($quality, $softSkillsCount);

        return $quality;
    }

    private function skillsQuality(int $quality, int $count): int
    {
        if (1 === $count) {
            $quality += 2;
        } elseif (2 === $count) {
            $quality += 3;
        } elseif ($count >= 3) {
            $quality += 6;
        }

        return $quality;
    }
}
