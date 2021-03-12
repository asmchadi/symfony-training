<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\OrderLineRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=OrderLineRepository::class)
 */
class OrderLine
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotNull(
     *     message="Please provide a valid Quantity"
     * )
     * @Assert\GreaterThanOrEqual(
     *     value="1",
     *     message="Please provide quantity >= 1"
     * )
     * @ORM\Column(type="integer")
     */
    private $quantity;

    /**
     * @ORM\ManyToOne(targetEntity=Product::class, inversedBy="orderLines", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $product;

    /**
     * @ORM\ManyToOne(targetEntity=Order::class, inversedBy="orderLines")
     * @ORM\JoinColumn(nullable=false)
     */
    private $cart;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getCart(): ?Order
    {
        return $this->cart;
    }

    public function setCart(?Order $cart): self
    {
        $this->cart = $cart;

        return $this;
    }

    /**
     * Computes the line total price.
     *
     * @return float the line total price
     */
    public function computeTotal(): float
    {
        return $this->getQuantity() * $this->getProduct()->getUnitPrice();
    }
}
