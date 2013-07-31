<?php

error_reporting(E_ALL);
ini_set("display_errors", "On");
ini_set("display_startup_errors", "On");
date_default_timezone_set("Europe/Helsinki");
//Autoload
require '../vendor/autoload.php';

\Slim\Slim::registerAutoloader();


$loader = new Twig_Loader_Filesystem('../templates');
$twig   = new Twig_Environment($loader, array(
        // 'cache' => 'cache',
        ));
\Slim\Extras\Views\Twig::$twigOptions = array(
    'debug' => true
);
$twig->addExtension(new Twig_Extensions_Extension_I18n());

\Slim\Extras\Views\Twig::$twigExtensions = array(
    'Twig_Extension_Debug',
    'Twig_Extensions_Extension_I18n'
);

$app                    = new \Slim\Slim(array(
    'templates.path'     => '../templates',
    'debug'              => true,
    'view'               => new \Slim\Views\Twig(),
    'cookies.secret_key' => md5('appsecretkey'),
    'log.enabled'        => TRUE,
    'log.writer'         => new \Slim\Extras\Log\DateTimeFileWriter(array(
        'path'           => '../logs',
        'name_format'    => 'Y-m-d',
        'message_format' => '%label% - %date% - %message%'
            ))
        ));
$app->view()->parserExtensions = array(
    'Twig_Extension_Debug',
    'Twig_Extensions_Extension_I18n'
);
$locality               = 'de_DE.UTF-8'; // locality should be determined here
if (defined('LC_MESSAGES'))
{
    /**
     * Set the specific locale information we want to change. We could also
     * use LC_MESSAGES, but because we may want to use other locale information
     * for things like number separators, currency signs, we'll say all locale
     * information should be updated.
     *
     * The second parameter is the locale and encoding you want to use. You
     * will need this locale and encoding installed on your system before you
     * can use it.
     *
     * On an Ubuntu/Debian system, adding a new locale is simple.
     *
     * $ sudo apt-get install language-pack-de # German
     * $ sudo apt-get install language-pack-ja # Japanese
     *
     * You can also generate a specific locale using locale-gen.
     *
     * $ sudo locale-gen en_US.UTF-8
     * $ sudo locale-gen de_DE.UTF-8
     */
    setlocale(LC_MESSAGES, $locality); // Linux
}
else
{
    putenv("LC_ALL={$locality}"); // windows
}

if (false === function_exists('gettext'))
{
    echo "You do not have the gettext library installed with PHP.";
    exit(1);
}
/**
 * Because the .po file is named messages.po, the text domain must be named
 * that as well. The second parameter is the base directory to start
 * searching in.
 */
bindtextdomain('messages', '../locale');

/**
 * Tell the application to use this text domain, or messages.mo.
 */
textdomain('messages');

$app->get('/',
          function () use ($app)
        {
            $app->view()->setData(array('name' => 'Vic'));
            $app->render('index.twig');
        });
$app->run();