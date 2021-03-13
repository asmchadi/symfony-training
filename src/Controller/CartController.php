<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\CartNotValidException;
use App\Form\CartType;
use App\Form\CheckoutType;
use App\Service\Cart;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    /**
     * Displays the cart page.
     *
     * @Route("/cart", name="cart")
     *
     * @param Cart    $cart    the cart manager
     * @param Request $request the request instance
     *
     * @return Response the response instance
     */
    public function index(Cart $cart, Request $request): Response
    {
        $myCart = $cart->getCart();
        $form = $this->createForm(CartType::class, $myCart);
        $form->handleRequest($request);
        if ($form->isSubmitted() === true) {
            if ($form->isValid() === true) {
                $cart->updateCart($myCart);
                $this->addFlash('success', 'Your cart has been updated.');

                return $this->redirectToRoute('cart', [], Response::HTTP_FOUND);
            }
        }

        return $this->render('cart/index.html.twig', [
            'cart' => $myCart,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Clears the cart.
     *
     * @Route("/cart/clear", name="clear")
     *
     * @param SessionInterface $session the current session to be cleared
     *
     * @return RedirectResponse the redirect response instance
     */
    public function clear(SessionInterface $session)
    {
        $session->clear();

        return $this->redirectToRoute('default', [], Response::HTTP_FOUND);
    }

    /**
     * Displays the checkout page.
     *
     * @Route("/checkout", name="checkout")
     *
     * @param Cart    $cart    The cart service
     * @param Request $request The request object
     *
     * @return Response the response instance
     *
     * @throws CartNotValidException
     */
    public function checkout(Cart $cart, Request $request): Response
    {
        $checkout = $cart->getCart();
        $form = $this->createForm(CheckoutType::class, $checkout);
        $form->handleRequest($request);
        if ($form->isSubmitted() === true) {
            if ($form->isValid() === true) {
                $cart->updateCart($checkout);
                $cart->saveToDatabase();
                $this->addFlash(
                    'success',
                    'Thanks for using our website. Your cart has been submitted.'
                );

                return $this->redirectToRoute('default', [], Response::HTTP_FOUND);
            } else {
                throw new CartNotValidException();
            }
        }

        return $this->render('cart/checkout.html.twig', [
            'form' => $form->createView(),
            'cart' => $cart,
        ]);
    }
}
