<?php
/** @phpstan-var \Twig\Environment $Twig */

echo $Twig->render('login/reset-password.twig', [
    'error' => 'Password recovery is disabled in this configuration.',
    'sent'  => false,
]);
