<?php

declare(strict_types=1);

namespace App\Twig;

use App\Entity\Order;
use App\Entity\OrderLine;
use App\Entity\Product;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class CartExtension extends AbstractExtension
{
    const LENGTH = 20;

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('totalCart', [$this, 'computeTotal']),
            new TwigFunction('totalOrderLine', [$this, 'computeLineTotal']),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getFilters()
    {
        return [
            new TwigFilter('minimize', [$this, 'minimizeProduct']),
            new TwigFilter('minimizeLabel', [CartExtensionRuntime::class, 'minimizeString']),
        ];
    }

    /**
     * Computes and returns the total amount of the given cart.
     *
     * @param Order $order the order instance
     *
     * @return string the formatted total
     */
    public function computeTotal(Order $order): string
    {
        $total = 0.0;
        foreach ($order->getOrderLines() as $line) {
            $total += ($line->getQuantity() * $line->getProduct()->getUnitPrice());
        }

        return \number_format($total, 2, ',', ' ');
    }

    /**
     * Computes and returns the total amooutof the given order line.
     *
     * @param OrderLine $line the order line
     *
     * @return string the formatted total
     */
    public function computeLineTotal(OrderLine $line): string
    {
        return \number_format(
            $line->getQuantity()*$line->getProduct()->getUnitPrice(),
            2,
            ',',
            ' '
        );
    }

    /**
     * Filter to cut the product's label to keep only first n characters.
     *
     * @param Product $product the product instance
     *
     * @return string the resulting string
     */
    public function minimizeProduct(Product $product): string
    {
        return \strlen($product->getLabel()) > self::LENGTH ?
            \sprintf(
                '%s...',
                \mb_substr($product->getLabel(), 0, self::LENGTH)
        ) : $product->getLabel();
    }
}
