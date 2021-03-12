<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\OrderLine;
use App\Entity\Product;
use App\Form\AddToCartType;
use App\Repository\ProductRepository;
use App\Service\Cart;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends AbstractController
{
    /**
     * Display details of a given product using the slug.
     *
     * @Route("/product/{slug}", name="product")
     *
     * @param Product $product the product instance
     * @param Request $request the request instance
     * @param ProductRepository $repository the repository instance
     * @param Cart $cart
     *
     * @return Response The response instance
     *
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function index(
        Product $product,
        Request $request,
        ProductRepository
        $repository,
        Cart $cart
    ): Response {
        $line = new OrderLine();
        $line->setProduct($product);
        $form = $this->createForm(AddToCartType::class, $line);
        $form->handleRequest($request);
        if ($form->isSubmitted() === true) {
            if ($form->isValid() === true) {
                $cart->addToCart($line);
                $this->addFlash('success', 'Product added to cart.');

                return $this->redirectToRoute('cart');
            }
        }
        /** @var array<Product> $related */
        $related = $repository->getRelatedProducts($product);

        return $this->render('product/index.html.twig', [
            'product' => $product,
            'related' => $related,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Performs the search of the products bya given word.
     *
     * @Route("/search", name="search")
     *
     * @param Request           $request    the request instance
     * @param ProductRepository $repository the repository instance
     *
     * @return Response the response instance
     */
    public function search(Request $request, ProductRepository $repository): Response
    {
        $q = $request->query->get('q');
        $products = $repository->search([
            'term' => $q,
        ]);

        return $this->render('default/index.html.twig', [
            'products' => $products,
        ]);
    }
}
