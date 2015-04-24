<?php

use yii\helpers\ArrayHelper;

$config = [
    'id' => 'dotplant2',
    'basePath' => dirname(__DIR__),
    'extensions' => require(__DIR__ . '/../vendor/yiisoft/extensions.php'),
    'language' => 'ru',
    'bootstrap' => [
        'core',
        'seo',
        'backend',
        'app\components\UserPreferencesBootstrap',

    ],
    'defaultRoute' => 'default',
    'modules' => [
        'user' => [
            'class' => 'app\modules\user\UserModule',
            'loginSessionDuration' => 2592000,
        ],
        'backend' => [
            'class' => 'app\backend\BackendModule',
            'layout' => '@app/backend/views/layouts/main',
            'administratePermission' => 'administrate',
        ],
        'background' => [
            'class' => 'app\backgroundtasks\BackgroundTasksModule',
            'layout' => '@app/backend/views/layouts/main',
            'controllerNamespace' => 'app\backgroundtasks\controllers',
            'notifyPermissions' => ['task manage'],
            'manageRoles' => ['admin'],
        ],
        'seo' => [
            'class' => 'app\seo\SeoModule',
            'include' => [
                'basic/default',
                'basic/page',
            ],
            'layout' => '@app/backend/views/layouts/main',
        ],
        'dynagrid' =>  [
            'class' => '\kartik\dynagrid\Module',
            'dbSettings' => [
                'tableName' => '{{%dynagrid}}',
            ],
            'dbSettingsDtl' => [
                'tableName' => '{{%dynagrid_dtl}}',
            ],
            'dynaGridOptions' => [
                'storage' => 'db',
                'gridOptions' => [
                    'toolbar' => [
                        '{dynagrid}',
                        '{toggleData}',
                        //'{export}',
                    ],
                    'export' => false,

                ],
            ],

        ],
        'gridview' =>  [
            'class' => '\kartik\grid\Module',

        ],
    ],
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'syscache' => [
            'class' => 'yii\caching\FileCache',
            'cachePath' => '@runtime/syscache',
        ],
        'request' => [
            'enableCsrfValidation' => true,
            'cookieValidationKey' => 'njandsfkasbf',
        ],
        'response' => [
            'class' => 'app\components\Response',
        ],
        'urlManager' => [
            'class' => 'app\components\UrlManager',
            'cache' => 'syscache',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'login/<service:google_oauth|facebook|etc>' => 'user/user/login',
                'login' => 'user/user/login',
                'logout' => 'user/user/logout',
                'signup' => 'user/user/signup',
                'cart/payment-result/<id:.+>' => 'cart/payment-result',
                'search' => 'default/search',
                'robots.txt' => 'seo/manage/get-robots',
                [
                    'class' => 'app\components\PageRule',
                ],
                [
                    'class' => 'app\components\ObjectRule',
                ],
            ],
        ],
        'assetManager' => [
            'class' => 'yii\web\AssetManager',
            'bundles' => require(__DIR__ . '/' . (YII_ENV_PROD ? 'assets-prod.php' : 'assets-dev.php')),
            'linkAssets' => YII_ENV_DEV,
        ],
        'user' => [
            'class' => '\yii\web\User',
            'identityClass' => 'app\modules\user\models\User',
            'enableAutoLogin' => true,
            'loginUrl' => ['/login'],
        ],
        'authManager' => [
            'class'=>'yii\\rbac\\DbManager',
            'cache' => 'cache',
        ],
        'authClientCollection' => [
            'class' => 'yii\authclient\Collection',
        ],
        'apiServiceClientCollection' => [
            'class' => 'yii\authclient\Collection',
            'clients' => [
                // имя клиента прописывается в Callback URI
                'yandexwebmaster' => [
                    'class' => 'yii\authclient\clients\YandexOAuth',
                    'clientId' => '3ba7c6d1cc474483832bbfed8050a8e0',
                    'clientSecret' => '3a3b8b551b7e4c70b05274cf62688784',
                ],
            ],
        ],
        'errorHandler' => [
            'class' => 'app\components\DotplantErrorHandler',
            'errorAction' => 'default/error',
        ],
        'mail' => file_exists(__DIR__ . '/email-config.php') ? require(__DIR__ . '/email-config.php') : [ 'class' => 'yii\swiftmailer\Mailer' ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 6 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'filterquery' => [
            'class' => 'app\components\filters\FilterQueryChain',
            'filters' => [
                [
                    'class' => 'app\components\filters\ProductPriceRangeFilter',
                ]
            ]
        ],
        'session' => [
            'timeout' => 2592000, // 30 days
        ],
    ],
];

$allConfig = ArrayHelper::merge(
    file_exists(__DIR__ . '/common.php') ? require(__DIR__ . '/common.php') : [],
    $config,
    file_exists(__DIR__ . '/../web/theme/module/config/common.php')
        ? require(__DIR__ . '/../web/theme/module/config/common.php')
        : [],

    file_exists(__DIR__ . '/common-configurables.php')
        ? require(__DIR__ . '/common-configurables.php')
        : [],

    file_exists(__DIR__ . '/../web/theme/module/config/web.php')
        ? require(__DIR__ . '/../web/theme/module/config/web.php')
        : [],

    file_exists(__DIR__ . '/web-configurables.php')
        ? require(__DIR__ . '/web-configurables.php')
        : [],


    file_exists(__DIR__ . '/common-local.php') ? require(__DIR__ . '/common-local.php') : [],
    file_exists(__DIR__ . '/web-local.php') ? require(__DIR__ . '/web-local.php') : []
);

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $allConfig['bootstrap'][] = 'debug';
    $allConfig['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'panels' => [
        ],
    ];
    $allConfig['modules']['gii'] = 'yii\gii\Module';
}

return $allConfig;
