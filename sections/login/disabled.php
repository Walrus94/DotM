<?php
/** @phpstan-var \Twig\Environment $Twig */

echo $Twig->render('login/disabled.twig', [
    'username' => '',
    'auto'     => false,
    'enabler'  => null,
]);
