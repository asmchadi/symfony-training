<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Order;
use App\Event\CartPlacedEvent;
use App\Form\ContactType;
use App\Model\Contact;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Service\Cart;
use App\Service\Mailer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * Displays the main page.
     *
     * @Route("/", name="default")
     *
     * @param ProductRepository $repository the repository
     *
     * @return Response the response instance
     */
    public function index(ProductRepository $repository): Response
    {
        return $this->render('default/index.html.twig', [
            'products' => $repository->getProductsWithQuantity(-1, 15),
        ]);
    }

    /**
     * Gets the  list of brands.
     *
     * @return Response The  response instance
     */
    public function brands(): Response
    {
        return $this->render('default/brands.html.twig', [
            'brands' => [
                'Nike' => 20,
                'Puma' => 10,
                'Adidas' => 8,
            ],
        ]);
    }

    /**
     * Renders the menu.
     *
     * @param CategoryRepository $repository the repository instance
     *
     * @return Response the  response instance
     */
    public function menu(CategoryRepository $repository): Response
    {
        $categories = $repository->findAll();

        return $this->render(
           'default/menu.html.twig',
           [
               'categories' => $categories,
           ]
        );
    }

    /**
     * Retrieves the user menu : cart, login and register.
     *
     * @param Cart $cart the cart manager
     *
     * @return Response the response instance
     */
    public function user(Cart $cart): Response
    {
        return $this->render('default/user.html.twig', [
            'cart' => $cart->getCart(),
        ]);
    }

    /**
     * Displays the contact form.
     *
     * @Route("/contact-us", name="contactus")
     *
     * @return Response the response object
     */
    public function contact(Request $request, Mailer $mailer): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);
        if ($form->isSubmitted() === true) {
            if ($form->isValid() === true) {
                $mailer->sendContactMail($contact);
                $this->addFlash('success', 'An email has been sent to the admin.');

                return $this->redirectToRoute('default', [], Response::HTTP_FOUND);
            }
        }

        return $this->render('default/contact.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Displays the search form.
     *
     * @return Response the response instance
     */
    public function searchForm(): Response
    {
        return $this->render('default/search_form.html.twig');
    }
}
