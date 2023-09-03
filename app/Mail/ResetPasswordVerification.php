<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordVerification extends Mailable
{
  use Queueable, SerializesModels;

  public $code;

  public function __construct($code)
  {
    $this->code = $code;
  }

  public function build()
  {
    return $this->view('emails.reset_password')
      ->with([
        'code' => $this->code,
      ]);
  }
}
