<?php

namespace App\Core\Mailer;

use App\Core\Repository\ConfigRepository;
use App\Core\Util\Arrays;
use App\Notification\Mailjet\Email as MailjetEmail;
use App\Notification\Twig\Email as TwigEmail;
use App\Recruiter\Entity\Recruiter;
use App\User\Entity\User;
use Nette\Utils\Json;
use Nette\Utils\JsonException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class Mailer
{
    private MailerInterface $mailer;
    private PropertyAccessorInterface $propertyAccessor;
    private ConfigRepository $configRepository;
    private ?string $emailsAuthorizedPattern = null;
    private string $env;
    private string $cluster;
    private LoggerInterface $logger;

    public function __construct(MailerInterface $mailer, LoggerInterface $logger, PropertyAccessorInterface $propertyAccessor, ConfigRepository $configRepository, string $env, string $cluster)
    {
        $this->mailer = $mailer;
        $this->propertyAccessor = $propertyAccessor;
        $this->configRepository = $configRepository;
        $this->env = $env;
        $this->cluster = $cluster;
        $this->logger = $logger;
    }

    public function send(Email $email): void
    {
        if (true === $this->isAuthorized($email->getTo()[0]->getAddress())) {
            try {
                $this->mailer->send($email);
            } catch (\Exception $e) {
                $this->logger->error('[MAILER] Sending error', [
                    'message' => $e->getMessage(),
                ]);
            }
        }
    }

    public function sendUser(TwigEmail $email, User $user): ?User
    {
        if (
            (null !== $emailNotification = $email->getNotification()) &&
            true === $user->getEnabled() && false === $user->isDeleted() &&
            (null !== $userNotification = $user->getNotification()) &&
            true === $this->propertyAccessor->getValue($userNotification, $emailNotification) &&
            null !== $userEmail = $user->getEmail()
        ) {
            $email->to($userEmail);

            $this->send($email);

            return $user;
        }

        return null;
    }

    public function sendRecruiter(MailjetEmail $email, Recruiter $recruiter): bool
    {
        if (null !== $recruiterEmail = $recruiter->getEmail()) {
            $email->to($recruiterEmail);

            $this->send($email);

            return true;
        }

        return false;
    }

    private function isAuthorized(string $email): bool
    {
        if ('test' === $this->env || \in_array($this->cluster, ['local', 'prod'], true)) {
            return true;
        }

        $pattern = $this->getEmailsAuthorizedPattern();

        return !empty($pattern) && 1 === preg_match($pattern, $email);
    }

    private function getEmailsAuthorizedPattern(): string
    {
        if (null !== $this->emailsAuthorizedPattern) {
            // cache
            return $this->emailsAuthorizedPattern;
        }

        $emailsAuthorized = $this->configRepository->findOneBy(['name' => 'emails_authorized']);
        if (null === $emailsAuthorized || empty($emailsAuthorized->getValue())) {
            // config not found in database (or empty)
            $this->emailsAuthorizedPattern = '';

            return $this->emailsAuthorizedPattern;
        }

        try {
            $emailAuthorizedValues = Json::decode($emailsAuthorized->getValue(), Json::FORCE_ARRAY);
        } catch (JsonException $e) {
            // config value is not json
            $this->emailsAuthorizedPattern = '';

            return $this->emailsAuthorizedPattern;
        }

        if (empty($emailAuthorizedValues)) {
            // config values is empty
            $this->emailsAuthorizedPattern = '';

            return $this->emailsAuthorizedPattern;
        }

        // build regex
        $this->emailsAuthorizedPattern .= '#' . implode('|', Arrays::map($emailAuthorizedValues, static function (string $v) {
            return "($v)";
        })) . '#';

        return $this->emailsAuthorizedPattern;
    }
}
