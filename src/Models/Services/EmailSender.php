<?php

namespace WalkerChiu\Newsletter\Models\Services;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\View;

class EmailSender extends Mailable
{
    use Queueable, SerializesModels;



    /**
     * Create a new sender instance.
     *
     * @param Array  $parameters
     * @param Array  $receiver
     * @param Array  $sender
     * @param Array  $replyTo
     * @param Array  $cc
     * @param Array  $bcc
     * @return void
     */
    public function __construct(array $parameters, array $receiver, array $sender, array $replyTo, array $cc, array $bcc)
    {
        $this->subject  = $parameters['subject'];
        $this->viewData = [
        	'email_theme'   => $parameters['theme'],
        	'email_style'   => $parameters['style'],
        	'email_header'  => $parameters['header'],
        	'email_content' => $parameters['content'],
        	'email_footer'  => $parameters['footer']
        ];
        $this->to      = $receiver;
        $this->from    = $sender;
        $this->replyTo = $replyTo;
        $this->cc      = $cc;
        $this->bcc     = $bcc;

        config()->set($parameters['smtp']);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $email = $this->from($this->from['email'], $this->from['name']);

        return View::exists('vendor.php-newsletter.emails.container') ?
                    $email->view('vendor.php-newsletter.emails.container') :
                    $email->view('php-newsletter::emails.container');
    }
}
