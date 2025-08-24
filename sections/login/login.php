<?php
/** @phpstan-var ?\Gazelle\User $Viewer */
/** @phpstan-var \Twig\Environment $Twig */

if (isset($Viewer)) {
    header("Location: /index.php");
    exit;
}

$login = new Gazelle\Login();
$watch = new Gazelle\LoginWatch($login->requestContext()->remoteAddr());

if (!empty($_POST['username']) && !empty($_POST['password'])) {
    $user = $login->login(
        username:   $_POST['username'],
        password:   $_POST['password'],
        watch:      $watch,
        twofa:      $_POST['twofa'] ?? '',
        persistent: $_POST['keeplogged'] ?? false,
    );

    if ($user) {
        $useragent = $_SERVER['HTTP_USER_AGENT'] ?? '[no-useragent]';
        $context = new Gazelle\BaseRequestContext(
            $_SERVER['SCRIPT_NAME'],
            $_SERVER['REMOTE_ADDR'],
            $useragent,
        );
        $session = new Gazelle\User\Session($user);
        $current = $session->create([
            'keep-logged' => $login->persistent() ? '1' : '0',
            'browser'     => $context->ua(),
            'ipaddr'      => $context->remoteAddr(),
            'useragent'   => $context->useragent(),
        ]);
        setcookie('session', $session->cookie($current['SessionID']), [
            'expires'  => (int)$login->persistent() * (time() + 60 * 60 * 24 * 90),
            'path'     => '/',
            'secure'   => !DEBUG_MODE,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        header("Location: index.php");
        exit;
    }
}

echo $Twig->render('login/login.twig', [
    'delta'    => $watch->bannedEpoch() - time(),
    'error'    => $login->error(),
    'ip_addr'  => $login->requestContext()->remoteAddr(),
    'tor_node' => (new Gazelle\Manager\Tor())->isExitNode(
        $login->requestContext()->remoteAddr()
    ),
    'watch'    => $watch,
]);
