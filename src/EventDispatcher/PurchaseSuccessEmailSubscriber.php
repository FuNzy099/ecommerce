<?php

namespace App\EventDispatcher;

use App\Entity\User;
use Psr\Log\LoggerInterface;
use App\Event\PurchaseSuccessEvent;
use Doctrine\ORM\Query\Expr\From;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mime\Address;

class PurchaseSuccessEmailSubscriber implements EventSubscriberInterface {

    protected $logger;
    protected $mailer;
    protected $security;

    public function __construct(LoggerInterface $logger, MailerInterface $mailer, Security $security)
    {
        $this->logger = $logger;   
        $this->mailer = $mailer;     
        $this->security = $security;
    }

    public static function getSubscribedEvents()
    {
        return [
            'purchase.success' => 'sendSuccessEmail'
        ];
    }

    public function sendSuccessEmail(PurchaseSuccessEvent $purchaseSuccessEvent) {

        // 1. Récupérer l'utilisateur actuellement en ligne (Pour connaitre son adresse en utilisant le service Sécurity)

        /** @var User */
        $currentUser = $this->security->getUser();

        // 2. Récupérer la commande (Je la trouve dans PurchaseSuccessEvent)
        $purchase = $purchaseSuccessEvent -> getPurchase();

        // 3. Ecrire le mail (Nouveau TemplatedEmail)
        $email = new TemplatedEmail;
        $email->to(new Address($currentUser->getEmail(), $currentUser->getFullName()))
            ->From("contact@email.com")
            ->subject("Bravo, votre commande ({$purchase->getId()}) a bien été confirmée !")
            ->htmlTemplate('emails/purchase_success.html.twig')
            ->context([
                'purchase' => $purchase,
                'user' => $currentUser,
            ]);

            $this->mailer->send($email);

        // 4. Envoyer le mail (Service MailerInterface)

        $this->logger->info("Email envoyé pour la commande n° ". $purchaseSuccessEvent->getPurchase()->getId());

    }

}