<?php


namespace App\Event;


use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserSubscriber implements EventSubscriberInterface
{
    private $mailer;

    public function __construct(Ma $mailer)
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
                    'emails/registration.html.twig',
                    ['user' => $event->getRegisteredUser()]
                ),
                'text/html'
            );

        $this->mailer->send($message);

    }
}