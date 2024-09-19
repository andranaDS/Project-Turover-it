<?php

namespace App\User\Contracts;

use App\User\Entity\User;
use App\User\Entity\UserDocument;

interface ResumeParserInterface
{
    public function parseResume(string $filepath, User $user, UserDocument $userDocument): ?User;
}
