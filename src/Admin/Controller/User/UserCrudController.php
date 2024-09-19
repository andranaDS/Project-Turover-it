<?php

namespace App\Admin\Controller\User;

use App\Admin\Field\EnumField;
use App\Admin\Field\FormationField;
use App\Admin\Field\LocationIqField;
use App\Admin\Field\NotificationField;
use App\Admin\Field\PhoneNumberField;
use App\Admin\Field\VichImageField;
use App\Admin\Form\Type\UserDocumentType;
use App\Admin\Form\Type\UserLanguageType;
use App\Admin\Form\Type\UserMobilityType;
use App\Admin\Form\Type\UserSkillType;
use App\Core\Enum\Currency;
use App\Core\Enum\EmploymentTime;
use App\Core\Enum\Gender;
use App\User\Entity\User;
use App\User\Enum\Availability;
use App\User\Enum\CompanyCountryCode;
use App\User\Enum\ExperienceYear;
use App\User\Enum\FreelanceLegalStatus;
use App\User\Manager\UserManager;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Security;

class UserCrudController extends AbstractCrudController
{
    private UserPasswordHasherInterface $hasher;
    private UserManager $um;
    private EntityManagerInterface $em;
    private Security $security;
    private EntityRepository $er;

    public function __construct(UserPasswordHasherInterface $hasher, UserManager $um, EntityManagerInterface $em, Security $security, EntityRepository $er)
    {
        $this->hasher = $hasher;
        $this->um = $um;
        $this->em = $em;
        $this->security = $security;
        $this->er = $er;
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Crud::PAGE_DETAIL)
        ;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->addFormTheme('@admin/formtypes/prosemirror.html.twig')
            ->setDefaultSort(['createdAt' => Criteria::DESC])
            ->setEntityLabelInPlural('Utilisateurs')
            ->setEntityLabelInSingular('Utilisateur')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        $id = IdField::new('id')->hideOnForm();

        $panelLogins = FormField::addPanel('Identifiants');
        $email = TextField::new('email', 'Email')->setColumns(6);
        $password = TextField::new('plainPassword', 'Mot de passe')->setRequired(Crud::PAGE_NEW === $pageName)->setColumns(6);

        $panelPersonalInfo = FormField::addPanel('Informations personnelles');
        $gender = EnumField::new('gender', 'Civilité')->setColumns(6)->setFormTypeOption('class', Gender::class);
        $lastname = TextField::new('lastname', 'Nom')->setColumns(6);
        $firstname = TextField::new('firstname', 'Prénom')->setColumns(6);
        $phone = PhoneNumberField::new('phone', 'Téléphone')->setColumns(6);
        $birthdate = DateTimeField::new('birthdate', 'Date de naissance')->setColumns(6);
        $location = LocationIqField::new('location', null)->useCities()->setColumns(6);
        $drivingLicense = BooleanField::new('drivingLicense', 'Permis de conduite')->setColumns(6);
        $profileJobTitle = TextField::new('profileJobTitle', 'Titre Actuel')->setColumns(6);
        $experienceYear = EnumField::new('experienceYear', 'Années d\'expériences')->setColumns(6)->setFormTypeOption('class', ExperienceYear::class);

        $panelForum = FormField::addPanel('Préférences du forum');
        $avatar = ImageField::new('avatar')->setTemplatePath('@admin/field/image.html.twig');
        $avatarFile = VichImageField::new('avatarFile', 'Avatar')
            ->setFormTypeOption('allow_delete', false)
            ->setHelp('Poids 30Mo max / Largeur 500px min - 4096px max / Hauteur 500px min - 4096px max')
        ;
        $displayAvatar = BooleanField::new('displayAvatar', 'Affichage de l\'avatar ?')->setColumns(12);
        $nickname = TextField::new('nickname', 'Pseudo')->setColumns(12);
        $website = UrlField::new('website', 'Site internet')->setColumns(6);
        $jobTitle = UrlField::new('jobTitle', 'Job')->setColumns(6);
        $signature = TextareaField::new('signature', 'Signature')->setCustomOption(TextareaField::OPTION_NUM_OF_ROWS, 6)->setColumns(12);

        $panelNotifications = FormField::addPanel('Notifications');
        $notification = NotificationField::new('notification', 'Préférences de notification')->setTemplatePath('@admin/field/notification.html.twig');

        $panelDocuments = FormField::addPanel('Documents');
        $documents = CollectionField::new('documents', 'Fichiers')->setEntryType(UserDocumentType::class)->setTemplatePath('@admin/field/documents.html.twig');

        $panelJobSearch = FormField::addPanel('Recherche de poste');
        $jobs = AssociationField::new('jobs', 'Métier recherché')->setColumns(6)->setTemplatePath('@admin/field/jobs.html.twig');
        $employmentTime = EnumField::new('employmentTime', 'Nb de jour/semaine')->setFormTypeOption('class', EmploymentTime::class)->setColumns(3);
        $fulltimeTeleworking = BooleanField::new('fulltimeTeleworking', '100% Remote')->setColumns(3);
        $freelance = BooleanField::new('freelance', 'Freelance')->setColumns(6);
        $employee = BooleanField::new('employee', 'Worker')->setColumns(6);
        $averageDailyRate = NumberField::new('averageDailyRate', 'TJM')->setColumns(4);
        $freelanceCurrency = EnumField::new('freelanceCurrency', 'Devise')->setFormTypeOption('class', Currency::class)->setColumns(2);
        $grossAnnualSalary = NumberField::new('grossAnnualSalary', 'Salaire annuel brut')->setColumns(4);
        $employeeCurrency = EnumField::new('employeeCurrency', 'Devise')->setFormTypeOption('class', Currency::class)->setColumns(2);
        $freelanceLegalStatus = EnumField::new('freelanceLegalStatus', 'Statut juridique')->setFormTypeOption('class', FreelanceLegalStatus::class)->setColumns(6);
        $newRow = FormField::addRow();
        $companyRegistrationNumber = TextField::new('companyRegistrationNumber', 'Siren')->setColumns(3);
        $companyCountryCode = EnumField::new('companyCountryCode', 'Pays')->setFormTypeOption('class', CompanyCountryCode::class)->setColumns(3);
        $newRow2 = FormField::addRow();
        $companyRegistrationNumberBeingAttributed = BooleanField::new('companyRegistrationNumberBeingAttributed', 'Siren en cours d\'attribution')->setColumns(6);
        $locations = CollectionField::new('locations', 'Mobilité')->setEntryType(UserMobilityType::class)->addCssClass('location-iqs');

        $panelFormation = FormField::addPanel('Formation');
        $formation = FormationField::new('formation', 'Dernier diplôme obtenu')->setTemplatePath('@admin/field/formation.html.twig');

        $panelSkills = FormField::addPanel('Compétances & Langues');
        $skills = CollectionField::new('skills', 'Compétences')->setColumns(6)->setEntryType(UserSkillType::class)->setTemplatePath('@admin/field/skills.html.twig');
        $softSkills = AssociationField::new('softSkills', 'Soft skills / Savoir être')->setColumns(6)->setTemplatePath('@admin/field/soft_skills.html.twig');
        $languages = CollectionField::new('languages', 'Langues')->setEntryType(UserLanguageType::class)->setColumns(12)->setTemplatePath('@admin/field/languages.html.twig');

        $panelAboutUs = FormField::addPanel('A propos de vous');
        $introduceYourself = TextareaField::new('introduceYourself', 'Présentation')->setCustomOption(TextareaField::OPTION_NUM_OF_ROWS, 6)->setColumns(12);
        $profileLinkedInProfile = UrlField::new('profileLinkedInProfile', 'Linkedin')->setColumns(4);
        $profileWebsite = UrlField::new('profileWebsite', 'SiteWeb')->setColumns(4);
        $profileProjectWebsite = UrlField::new('profileProjectWebsite', 'Linkedin')->setColumns(4);

        $panelVisibilityAndAvailability = FormField::addPanel('Visibilité & disponibilité');
        $visible = BooleanField::new('visible', 'Visible ?')->setColumns(6);
        $availability = EnumField::new('availability', 'Disponibilité')->setFormTypeOption('class', Availability::class)->setColumns(6);
        $nextAvailabilityAt = DateTimeField::new('nextAvailabilityAt', 'Date');

        $panelApplications = FormField::addPanel('Candidatures');
        $applications = CollectionField::new('applications', 'Offres postulés & Candidatures spontanées')->setTemplatePath('@admin/field/applications.html.twig');

        $panelOther = FormField::addPanel('Autres informations');
        $forumPostUpvotesCount = IntegerField::new('forumPostUpvotesCount', 'Nombre de like des posts du forum');
        $forumPostsCount = IntegerField::new('forumPostsCount', 'Nombre de posts du forum');
        $activeJobPostingSearchesCount = IntegerField::new('activeJobPostingSearchesCount', 'Nombre d\'alertes actives');
        $ip = TextField::new('ip', 'IP à la création');
        $createdAt = DateTimeField::new('createdAt', 'Date de création')->hideOnForm();
        $passwordUpdatedAt = DateTimeField::new('passwordUpdatedAt', 'Date de mise à jour du mot de passe');
        $profileCompleted = BooleanField::new('profileCompleted', 'Profile complété ?');
        $locked = BooleanField::new('locked', 'Vérouillé ?');
        $lockedBy = AssociationField::new('lockedBy', 'Vérouillé par');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$id, $lastname, $firstname, $email, $createdAt, $locked];
        }
        if (Crud::PAGE_EDIT === $pageName || Crud::PAGE_NEW === $pageName) {
            return [
                $panelLogins, $email, $password,
                $panelPersonalInfo, $gender, $phone, $lastname, $firstname, $birthdate, $location, $profileJobTitle, $experienceYear, $drivingLicense,
                $panelForum, $avatarFile, $displayAvatar, $nickname, $website, $jobTitle, $signature,
                $panelNotifications, $notification,
                $panelDocuments, $documents,
                $panelJobSearch, $jobs, $employmentTime, $fulltimeTeleworking, $freelance, $employee, $averageDailyRate, $freelanceCurrency, $grossAnnualSalary, $employeeCurrency, $freelanceLegalStatus, $newRow, $companyRegistrationNumber, $companyCountryCode, $newRow2, $companyRegistrationNumberBeingAttributed, $locations,
                $panelFormation, $formation,
                $panelSkills, $skills, $softSkills, $languages,
                $panelAboutUs, $introduceYourself, $profileLinkedInProfile, $profileWebsite, $profileProjectWebsite,
                $panelVisibilityAndAvailability, $visible, $availability, $nextAvailabilityAt,
                $panelOther, $locked,
            ];
        }
        if (Crud::PAGE_DETAIL === $pageName) {
            return [
                $panelLogins, $id, $email,
                $panelPersonalInfo, $gender, $lastname, $firstname, $phone, $birthdate, $location, $profileJobTitle, $experienceYear, $drivingLicense,
                $panelForum, $avatar, $displayAvatar, $nickname, $website, $jobTitle, $signature,
                $panelNotifications, $notification,
                $panelDocuments, $documents,
                $panelJobSearch, $jobs, $employmentTime, $fulltimeTeleworking, $freelance, $employee, $averageDailyRate, $freelanceCurrency, $grossAnnualSalary, $employeeCurrency, $freelanceLegalStatus, $newRow, $companyRegistrationNumber, $companyCountryCode, $newRow2, $companyRegistrationNumberBeingAttributed, $locations,
                $panelFormation, $formation,
                $panelSkills, $skills, $softSkills, $languages,
                $panelAboutUs, $introduceYourself, $profileLinkedInProfile, $profileWebsite, $profileProjectWebsite,
                $panelVisibilityAndAvailability, $visible, $availability, $nextAvailabilityAt,
                $panelApplications, $applications,
                $panelOther, $ip, $createdAt, $passwordUpdatedAt, $profileCompleted, $locked, $lockedBy, $forumPostUpvotesCount, $forumPostsCount, $activeJobPostingSearchesCount,
            ];
        }

        return [];
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $response = $this->er->createQueryBuilder($searchDto, $entityDto, $fields, $filters);
        $response->andWhere('entity.deletedAt IS NULL');

        return $response;
    }

    // @phpstan-ignore-next-line
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->encodePassword($entityInstance);
        parent::persistEntity($entityManager, $entityInstance);
    }

    // @phpstan-ignore-next-line
    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->encodePassword($entityInstance);
        parent::updateEntity($entityManager, $entityInstance);
    }

    // @phpstan-ignore-next-line
    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        // @phpstan-ignore-next-line
        $this->um->deleteUser($entityInstance, $this->security->getUser());
        $this->em->flush();
    }

    /** TODO useless */
    private function encodePassword(User $user): void
    {
        if (null !== $user->getPlainPassword()) {
            $user->setPassword($this->hasher->hashPassword($user, $user->getPlainPassword()));
        }
    }
}
