<?php

switch ($_REQUEST['action'] ?? 'view') {
    case 'add':
        include_once 'add.php';
        break;

    case 'remove':
        include_once 'remove.php';
        break;

    case 'mass_edit':
        include_once 'mass_edit.php';
        break;

    case 'remove_snatched':
        include_once 'remove_snatched.php';
        break;

    case 'edit':
        match ($_REQUEST['type'] ?? '') {
            'torrents' => include_once 'edit_torrents.php',
            default    => error(404),
        };
        break;

    case 'view':
        switch ($_REQUEST['type'] ?? 'torrents') {
            case 'torrents':
                include_once 'torrents.php';
                break;
            case 'artists':
                include_once 'artists.php';
                break;
            case 'collages':
                $_GET['bookmarks'] = '1';
                include_once __DIR__ . '/../collages/browse.php';
                break;
            case 'requests':
                // Request system disabled for music catalog
                error('Request system has been disabled for music catalog');
                break;
            default:
                error(404);
        }
        break;

    default:
        error(404);
}
