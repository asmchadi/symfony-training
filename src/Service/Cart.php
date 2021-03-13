<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Order;
use App\Entity\OrderLine;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Cart
{
    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * @var Mailer
     */
    private $mailer;

    /** @var LoggerInterface */
    private $logger;
    /**
     * @var ProductRepository
     */
    private $repository;

    public function __construct(
        SessionInterface $session,
        EntityManagerInterface $manager,
        Mailer $mailer,
        ProductRepository $repository
    ) {
        $this->session = $session;
        $this->manager = $manager;
        $this->mailer = $mailer;
        $this->repository = $repository;
    }

    /**
     * Adds a line to the order.
     *
     * @param OrderLine $line the order line to add
     *
     * @throws \Exception
     */
    public function addToCart(OrderLine $line)
    {
        $order = $this->initialize();
        $order->addOrderLine($line);
        $this->session->set('cart', $order);
    }

    /**
     * Gets the current session from the session or null if none set.
     *
     * @return Order|null gets the current cart
     */
    public function getCart(): ?Order
    {
        /** @var Order|null $order */
        $order = $this->session->has('cart') ? $this->session->get('cart') : null;

        return $order;
    }

    /**
     * Initialize the cart in session if not already set.
     *
     * @return Order The order
     *
     * @throws \Exception
     */
    private function initialize()
    {
        $order = null;
        if ($this->session->has('cart') === false) {
            $order = new Order();
            $order->setCreateAt(new \DateTime())
                ->setUpdateAt(null);
            $this->session->set('cart', $order);
        } else {
            /** @var Order $order */
            $order = $this->session->get('cart');
        }

        return $order;
    }

    /**
     * Updates the order instance in the session.
     *
     * @param Order $order the new instance of the order
     */
    public function updateCart(Order $order)
    {
        if ($this->session->has('cart') === true) {
            $this->session->set('cart', $order);
        }
    }

    /**
     * Saves the cart into the database and clears the session. It will notify the user by email.
     */
    public function saveToDatabase()
    {
        $cart = $this->getCart();
        $cart->setStatus(Order::ORDER_PLACED);
        foreach ($cart->getOrderLines() as $line) {
           $this->manageQuantity($line);
        }
        $this->manager->persist($cart);
        $this->manager->flush();
        $this->mailer->sendMail($cart);
        $this->clearCart();
        $this->logger->info('Order_Place', [
            'message' => 'Order placed success',
            'order' => $cart,
        ]);
    }

    /**
     * @required
     *
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * Clears the session.
     */
    private function clearCart()
    {
        $this->session->clear();
    }

    /**
     * Deduct the ordered quantity from the product total quantity.
     *
     * @param Product $product  the product instance
     * @param int     $quantity the new quantity
     */
    private function updateProductQuantity(Product $product, int $quantity)
    {
        $product->setQuantity($product->getQuantity() - $quantity);
    }

    /**
     * Manages the quantity of the order line and the product.
     *
     * @param OrderLine $line the order line instance
     */
    private function manageQuantity(OrderLine $line)
    {
        /** @var Product $product */
        $product = $this->repository->find($line->getProduct());
        $line->setProduct($product);
        $this->manager->persist($line);
        $this->updateProductQuantity($product, $line->getQuantity());
        $this->manager->persist($product);
    }
}
