<?php

use Silex\Provider\DoctrineServiceProvider;
use Dflydev\Provider\DoctrineOrm\DoctrineOrmServiceProvider;
use Silex\Provider\SecurityServiceProvider;
use Symfony\Component\Security\Core\Encoder\PlaintextPasswordEncoder;


// configure your app for the production environment

$app['twig.path'] = array(__DIR__ . '/../templates');
$app['twig.options'] = array('cache' => __DIR__ . '/../var/cache/twig');

include __DIR__.'/config.php'; // $dbOption
$app->register(new DoctrineServiceProvider(), $dbOption);

$app->register(new DoctrineOrmServiceProvider(),
    [
        'orm.proxies_dir' => sys_get_temp_dir(),
        'orm.em.options' => [
            'mappings' => [
                [
                    'type' => 'annotation',
                    'namespace' => 'Model',
                    'path' => __DIR__ . '/../src/Model',
                ]
            ]
        ]
    ]
);

$app->register(
    new SecurityServiceProvider(),
    [
        'security.firewalls' => [
            'firewall_admin' => [                       // Firewall name
                'pattern' => '^/user',                 // Firewall scope
                'form' => [
                    'login_path' => '/signin',
                    'check_path' => '/user/signin_check',
                    'failure_path' => '/signin'
                ],
                'users' => function() use ($app){
                    $repository = $app['orm.em']->getRepository(Model\User::class);
                    return new \Provider\DBUserProvider($repository);
                },
                'logout' => [
                    'logout_path' => '/logout',
                    'invalidate_session' => true,
                    'target_url' => '/user'
                ]
            ]
        ],
        'security.role_hierarchy' => [                  // Role hierarchy definition
            'ROLE_ADMIN' => ['ROLE_USER']               // Role admin is upper than role user
        ],
        'security.default_encoder' => function () {
            return new PlaintextPasswordEncoder();
        },
    ]
);

$app->register(new \Silex\Provider\SessionServiceProvider());

$app->register(new \Silex\Provider\ValidatorServiceProvider());

$app->register(new \Silex\Provider\FormServiceProvider());

$app->register(new \Silex\Provider\CsrfServiceProvider());

$app['locale'] = 'en_en';

$app->register(new \Silex\Provider\TranslationServiceProvider(),
    [
        'translator.domains ' => [],
    ]
);

$app->register(new Silex\Provider\SwiftmailerServiceProvider(), $swiftMaillerConfig);