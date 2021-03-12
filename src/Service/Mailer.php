<?php

declare(strict_types=1);

namespace App\Service;


use App\Entity\Order;
use App\Model\Contact;
use Twig\Environment;

class Mailer
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;
    /**
     * @var string
     */
    private $from;
    /**
     * @var Environment
     */
    private $twig;

    public function __construct(\Swift_Mailer $mailer, string $from, Environment $twig)
    {
        $this->mailer = $mailer;
        $this->from = $from;
        $this->twig = $twig;
    }

    /**
     * Sends mails.
     *
     * @param Order $cart the user's order
     */
    public function sendMail(Order $cart)
    {
        try {
            $message = (new \Swift_Message('Your order is placed'))
                ->setFrom($this->from)
                ->setTo($cart->getShipping()->getEmail())
                ->setBody(
                    $this->twig->render(
                        'email/cart_placed.html.twig',
                        ['cart' => $cart]
                    ),
                    'text/html'
                )/*->addPart(
                    $this->renderView(
                    // templates/emails/registration.txt.twig
                        'emails/registration.txt.twig',
                        ['name' => $name]
                    ),
                    'text/plain'
                )*/
            ;

            $this->mailer->send($message);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function sendContactMail(Contact $form)
    {
        $message = (new \Swift_Message('New message from contact form'))
            ->setFrom($form->getEmail())
            ->setTo($this->from)
            ->setBody(
                $this->twig->render(
                    'email/contact.html.twig',
                    ['contact' => $form]
                ),
                'text/html'
            );

        $this->mailer->send($message);
    }
}