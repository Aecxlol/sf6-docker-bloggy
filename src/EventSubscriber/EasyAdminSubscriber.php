<?php

namespace App\EventSubscriber;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class EasyAdminSubscriber implements EventSubscriberInterface
{
    public function __construct(private UserPasswordHasherInterface $userPasswordHasherInterface)
    {
    }

    public function onBeforeEntityPersistedEvent(BeforeEntityPersistedEvent $event): void
    {
        $entityInstance = $event->getEntityInstance();

        $this->hashPasswordIfUserAndPlainPasswordAreProvided($entityInstance);
    }

    public function onBeforeEntityUpdatedEvent(BeforeEntityUpdatedEvent $event): void
    {
        $entityInstance = $event->getEntityInstance();

        $this->hashPasswordIfUserAndPlainPasswordAreProvided($entityInstance);
    }

    private function hashPasswordIfUserAndPlainPasswordAreProvided($entityInstance)
    {
        if($entityInstance instanceof User && $entityInstance->getPlainPassword()) {
            $plainPassword = $entityInstance->getPlainPassword();
            $hashedPassword = $this->userPasswordHasherInterface->hashPassword($entityInstance, $plainPassword);

            $entityInstance->setPassword($hashedPassword);
        }
    }

    /**
     * @return string[]
     */
    #[ArrayShape([BeforeEntityPersistedEvent::class => "string", BeforeEntityUpdatedEvent::class => "string"])] public static function getSubscribedEvents(): array
    {
        return [
            BeforeEntityPersistedEvent::class => 'onBeforeEntityPersistedEvent',
            BeforeEntityUpdatedEvent::class => 'onBeforeEntityUpdatedEvent',
        ];
    }
}
