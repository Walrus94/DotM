<?php
/** @phpstan-var \Gazelle\User $Viewer */
/** @phpstan-var \Twig\Environment $Twig */

// The administrator is the only account in this demo environment.
$manager = new Gazelle\Manager\User();
$user = $manager->findById((int)($_REQUEST['id'] ?? 0));
if (is_null($user)) {
    error(404);
}
if ($user->id() !== $Viewer->id()) {
    error(403);
}

echo $Twig->render('user/admin-edit.twig', [
    'user'   => $user,
    'viewer' => $Viewer,
]);
