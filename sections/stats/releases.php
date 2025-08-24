<?php
/** @phpstan-var \Twig\Environment $Twig */

$statsTor = new Gazelle\Stats\Release();
$flow = $statsTor->flow();

echo $Twig->render('stats/release.twig', [
    'flow' => [
        'month' => array_values(array_map(fn($m) => $m['Month'], $flow)),
        'add'   => array_values(array_map(fn($m) => $m['t_add'], $flow)),
        'del'   => array_values(array_map(fn($m) => $m['t_del'], $flow)),
        'net'   => array_values(array_map(fn($m) => $m['t_net'], $flow)),
    ],
    'category' => $statsTor->categoryTotal(),
]);
