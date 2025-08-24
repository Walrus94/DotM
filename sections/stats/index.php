<?php
/** @phpstan-var \Twig\Environment $Twig */

switch ($_REQUEST['action'] ?? null) {
    case 'users':
        include_once 'users.php';
        break;
    case 'releases':
        include_once 'releases.php';
        break;
    default:
        echo $Twig->render('stats.twig');
        break;
}
