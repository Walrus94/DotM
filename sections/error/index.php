<?php
// phpcs:disable PSR1.Files.SideEffects.FoundWithSymbols
/** @phpstan-var \Gazelle\User $Viewer */
/** @phpstan-var \Twig\Environment $Twig */

switch ($Error) {
    case '403':
        $Title = "Error 403";
        $Description = "You tried to go to a page that you don't have enough permission to view.";
        break;
    case '404':
        $Title = "Error 404";
        $Description = "You tried to go to a page that doesn't exist.";
        break;
    case '429':
        $Title = "Error 429";
        $Description = "You tried to do something too frequently.";
        break;
    default:
        if (empty($Error)) {
            $Title = "Unexpected Error";
            $Description = "You have encountered an unexpected error.";
        } else {
            $Title = 'Error';
            $Description = $Error;
        }
}

if (isset($Log) && $Log) {
    $Description .= ' <a href="log.php?search=' . $Log . '">Search Log</a>';
}

if (empty($NoHTML) && isset($Error) && $Error != -1) {
    echo $Twig->render('error.twig', [
        'title'       => $Title,
        'description' => $Description,
    ]);
} else {
    echo $Description;
}
