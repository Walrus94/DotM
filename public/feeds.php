<?php

// Prevent people from clearing feeds
if (isset($_GET['clearcache'])) {
    unset($_GET['clearcache']);
}

require_once __DIR__ . '/../lib/bootstrap.php';

$user = (new Gazelle\Manager\User())->findByAnnounceKey($_GET['passkey'] ?? '');
$feed = new Gazelle\Feed();
if (
    !$user?->isEnabled()
    || empty($_GET['feed'])
    || empty($_GET['auth'])
    || $user->rssAuth($_GET['feed']) !== $_GET['auth']
) {
    // phpcs:disable Generic.PHP.ForbiddenFunctions.Found
    if (md5($user->id() . RSS_HASH . $_GET['passkey']) !== $_GET['auth']) {
        die($feed->blocked());
    }
    // phpcs:enable Generic.PHP.ForbiddenFunctions.Found
}

if (
    !empty($_SERVER['HTTP_X_FORWARDED_FOR'])
    && proxyCheck($_SERVER['REMOTE_ADDR'])
    && filter_var($_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)
) {
    $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_X_FORWARDED_FOR'];
}
$context = new Gazelle\BaseRequestContext(
    $_SERVER['SCRIPT_NAME'],
    $_SERVER['REMOTE_ADDR'],
    $_SERVER['HTTP_USER_AGENT'] ?? '[no-useragent]',
);
if ($user->permitted('site_disable_ip_history')) {
    $context->anonymize();
    $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
}
Gazelle\Base::setRequestContext($context);

switch ($_GET['feed']) {
    case 'releases_abooks':
    case 'releases_all':
    case 'releases_apps':
    case 'releases_comedy':
    case 'releases_comics':
    case 'releases_ebooks':
    case 'releases_evids':
    case 'releases_flac':
    case 'releases_lossless':
    case 'releases_lossless24':
    case 'releases_mp3':
    case 'releases_music':
    case 'releases_vinyl':
        echo $feed->byFeedName($user, $_GET['feed']);
        break;
    case 'feed_news':
        echo $feed->news(new Gazelle\Manager\News());
        break;
    case 'feed_blog':
        echo $feed->blog(new Gazelle\Manager\Blog(), new Gazelle\Manager\ForumThread());
        break;
    case 'feed_changelog':
        echo $feed->changelog(new Gazelle\Manager\Changelog());
        break;
    default:
        echo match (true) {
            str_starts_with($_GET['feed'], 'releases_bookmarks_e_') => $feed->bookmark($user, $_GET['feed']),
            str_starts_with($_GET['feed'], 'releases_notify_')      => $feed->personal($user, $_GET['feed'], $_GET['name'] ?? null),
            default => $feed->blocked()
        };
}
