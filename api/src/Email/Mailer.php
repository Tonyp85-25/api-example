<?php
namespace App\Email;
//use Symfony\Component\Mime\Email;

use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class Mailer
{
  
    public function __construct(private MailerInterface $mailer)
    {
        
    }

    public function sendMail(User $user) 
    {
    $email = (new TemplatedEmail())
    ->from('fabien@example.com')
    ->to(new Address($user->getEmail())) //a string is accepted as well
    ->subject('Please confirm your account!')

    // path of the Twig template to render
    ->htmlTemplate('emails/signup.html.twig')

    // pass variables (name => value) to the template
    ->context([
        'expiration_date' => new \DateTime('+7 days'),
        'user' => $user,
    ]);
    $this->mailer->send($email);
    }
    // $message = new Email()
}