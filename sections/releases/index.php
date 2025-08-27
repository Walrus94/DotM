<?php
/** @phpstan-var \Gazelle\User $Viewer */
/** @phpstan-var \Twig\Environment $Twig */

if (!isset($_GET['action'])) {
    $_GET['action'] = 'search';
}

switch ($_GET['action']) {
    case 'view':
        require_once 'view.php';
        break;
    case 'search':
    default:
        require_once 'search.php';
        break;
}