<?php

namespace App\EventDispatcher;

use Psr\Log\LoggerInterface;
use App\Event\ProductViewEvent;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProductViewEmailSubscriber implements EventSubscriberInterface {

    private $logger;
    protected $mailer;

    public function __construct(LoggerInterface $logger, MailerInterface $mailer)
    {
        $this->logger = $logger;
        $this->mailer = $mailer;
    }

    public static function getSubscribedEvents()
    {
        return [
            'product.view' => 'sendEmail',
        ];
    }

    public function sendEmail(ProductViewEvent $productViewEvent){

        // $email = new TemplatedEmail();
        // $email->from(new Address("contact@mail.com", "Infos de la boutique"))
        //     ->to("admin@mal.com")
        //     ->text("Un visiteur est en train de voir la page du prduit n°".$productViewEvent->getProduct()->getId())
        //     ->htmlTemplate('emails/product_view.html.twig')
        //     ->context([
        //         'product' => $productViewEvent -> getProduct()
        //     ])
        //     ->subject("Visite du produit n°".$productViewEvent->getProduct()->getId());

        //     $this->mailer->send($email);

        $this->logger->info("Email envoyé à l'admin pour le produit :"). $productViewEvent->getProduct()->getId();

    }
}