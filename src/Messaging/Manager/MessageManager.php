<?php

namespace App\Messaging\Manager;

use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Messaging\Entity\Feed;
use App\Messaging\Entity\Message;
use App\User\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MessageManager
{
    private EntityManagerInterface $em;
    private ValidatorInterface $validator;

    public function __construct(EntityManagerInterface $em, ValidatorInterface $validator)
    {
        $this->em = $em;
        $this->validator = $validator;
    }

    /**
     * @param bool|float|int|string|null $contentHtml
     * @param bool|float|int|string|null $contentJson
     */
    public function postMessage(?User $user, Feed $feed, ?File $uploadedFile, $contentHtml, $contentJson): Message
    {
        $message = (new Message())
            ->setFeed($feed)
            ->setContentHtml((string) $contentHtml)
            ->setContentJson((string) $contentJson)
            ->setAuthor($user)
        ;

        if ($uploadedFile instanceof UploadedFile) {
            $message
                ->setDocumentOriginalName($uploadedFile->getClientOriginalName())
                ->setDocumentFile($uploadedFile)
            ;
        }

        $this->validator->validate($message, ['groups' => ['Default', 'message:post']]);

        $this->em->persist($message);
        $this->em->flush();

        return $message;
    }
}
