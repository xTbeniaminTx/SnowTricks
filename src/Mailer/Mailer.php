<?php


namespace App\Mailer;


use App\Entity\User;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class Mailer
{
    /**
     * @var MailerInterface
     */
    private $mailer;
    /**
     * @var Environment
     */
    private $twig;
    /**
     * @var string
     */
    private $mailFrom;

    public function __construct(MailerInterface $mailer, Environment $twig, string $mailFrom)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->mailFrom = $mailFrom;

    }

    public function sendConfirmationEmail(User $user)
    {

        $message = (new Email())
            ->from($this->mailFrom)
            ->to($user->getEmail())
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Time for Symfony Mailer!')
            ->html(
                $this->twig->render(
                    'emails/registration.html.twig',
                    ['user' => $user]
                )
            );

        $this->mailer->send($message);
    }

}