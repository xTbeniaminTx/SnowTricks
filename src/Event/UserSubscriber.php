<?php


namespace App\Event;


use Symfony\Bridge\Monolog\Handler\SwiftMailerHandler;
use Symfony\Bundle\MonologBundle\SwiftMailer\MessageFactory;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Twig\Environment;

class UserSubscriber implements EventSubscriberInterface
{
    private $mailer;
    private $twig;

    public function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;

    }

    public static function getSubscribedEvents()
    {
        return [
            UserRegisterEvent::NAME => 'onUserRegister'
        ];
    }

    public function onUserRegister(UserRegisterEvent $event)
    {
        $message = (new \Swift_Message('Hello Email'))
            ->setFrom('send@example.com')
            ->setTo('recipient@example.com')
            ->setBody(
                $this->renderView(
                // templates/emails/registration.html.twig
                    'emails/registration.html.twig',
                    ['user' => $event->getRegisteredUser()]
                ),
                'text/html'
            );

        $this->mailer->send($message);

    }
}