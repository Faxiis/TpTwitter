<?php

namespace App\Util;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;

class LoginLoggerListener
{
    private string $logFile;

    public function __construct(string $projectDir)
    {
        $this->logFile = $projectDir . '/var/log/login_log.txt';
    }

    public function onAuthenticationSuccess(AuthenticationSuccessEvent $event)
    {
        $user = $event->getUser();

        if (!method_exists($user, 'getUsername')) {
            return;
        }

        $message = sprintf(
            "[%s] Connexion réussie pour l'utilisateur : %s\n",
            (new \DateTime())->format('Y-m-d H:i:s'),
            $user->getUsername()
        );

        file_put_contents($this->logFile, $message, FILE_APPEND);
    }
}