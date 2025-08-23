<?php
/** @phpstan-var \Gazelle\User $Viewer */
/** @phpstan-var \Twig\Environment $Twig */

Text::$TOC = true;

$newsMan = new Gazelle\Manager\News();

echo $Twig->render('index/private-sidebar.twig', [
    'blog'       => new Gazelle\Manager\Blog(),
    'staff_blog' => new Gazelle\Manager\StaffBlog(),
    'user_stats' => new Gazelle\Stats\Users(),
    'viewer'     => $Viewer,
]);

echo $Twig->render('index/private-main.twig', [
    'admin' => (int)$Viewer->permitted('admin_manage_news'),
    'news'  => $newsMan->headlines(),
]);

