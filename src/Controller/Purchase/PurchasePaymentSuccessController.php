<?php

namespace App\Controller\Purchase;

use App\Entity\Purchase;
use App\Cart\CartService;
use App\Event\PurchaseSuccessEvent;
use Doctrine\ORM\EntityManager;
use App\Repository\PurchaseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class PurchasePaymentSuccessController extends AbstractController
{
    /**
     * @Route("/purchase/terminate/{id}", name="purchase_payment_success")
     * @IsGranted("ROLE_USER", message="Vous devez être connecté pour passer une commande")
     */
    public function success($id, PurchaseRepository $purchaseRepository, EntityManagerInterface $em, CartService $cartService, EventDispatcherInterface $dispatcher)
    {
        //je recupere la commande
        $purchase = $purchaseRepository->find($id);

        if (
            !$purchase || 
            ($purchase && $purchase->getUser() !== $this->getUser()) || 
            ($purchase && $purchase->getStatus() === Purchase::STATUS_PAID)
            ) {
                $this->addFlash('warning', 'La commande n\'existe pas ! ');
                return $this->redirectToRoute('purchases_index');
            }

        //Je la fait passer au staut payé
        $purchase->setStatus(Purchase::STATUS_PAID);

        $em->flush($purchase);

        //Je vide le panier
        $cartService->empty();

        //lancer un evenement qui permet aux autres developpeur de ragir lors de la prise de commande
        $purchaseEvent =  new PurchaseSuccessEvent($purchase);
        $dispatcher->dispatch($purchaseEvent, 'purchase.success');

        //Reirection vers la liste des commandes
        $this->addFlash('success', 'La commande a été payé et confirmée ! ');   

        return $this->redirectToRoute('purchases_index');

    }
}