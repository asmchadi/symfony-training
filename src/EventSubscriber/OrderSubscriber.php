<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Event\CartPlacedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Security;

class OrderSubscriber implements EventSubscriberInterface
{
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var Security
     */
    private $token;

    public function __construct(Security $token, RouterInterface $router)
    {
        $this->router = $router;
        $this->token = $token;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => [
                ['kernelRequest', 1],
            ],
            KernelEvents::CONTROLLER => 'controllerEvent',
            CartPlacedEvent::NAME => 'orderPlaced',
        ];
    }

    /**
     * Fired when the event kernel.request is dispatched. It checks if a user is logged in and
     * the route called is = admin
     *
     * @param RequestEvent $event the Request Event Instance
     */
    public function kernelRequest(RequestEvent $event)
    {
        dump($route = $event->getRequest()->get('_route'));
        dump($this->token->getUser());
        if ('admin' === $route && $this->token->getUser() !== null) {
            // Generating some URL via the RouterInterface
            $url = $this->router->generate('some_route' /*, some parameters*/);

            //... some logic

            //<editor-fold desc="Creating a redirection to the some_route route">

            $redirect = new RedirectResponse($url);
            $event->setResponse($redirect);

            //</editor-fold>
        }
    }

    /**
     * Fired when the order.placed event is dispatched.
     *
     * @param CartPlacedEvent $event The order event instance
     */
    public function orderPlaced(CartPlacedEvent $event)
    {
        // ... some logic
    }

    public function controllerEvent(ControllerEvent $event)
    {
        dump($event->getController());
    }
}