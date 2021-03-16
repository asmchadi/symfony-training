<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Event\CartPlacedEvent;
use App\Service\Mailer;

class OrderListener
{
    /**
     * @var Mailer
     */
    private $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * This function respects the naming convention of the event.
     * The correct name on+CamelCase event name. For exemple the Event name is order.placed.
     *
     * @param CartPlacedEvent $event the event to be catched when the cart is placed
     */
    public function onOrderPlaced(CartPlacedEvent $event)
    {
        $this->mailer
            ->notifyAdmin($event->getOrder());
    }
}