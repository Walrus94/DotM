<?php
/** @phpstan-var \Twig\Environment $Twig */

echo $Twig->render('login/new-password.twig', [
    'error'     => 'Password reset is disabled in this configuration.',
    'key'       => $_GET['key'] ?? '',
    'success'   => false,
]);
