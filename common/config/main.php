<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
    ],
    'container' => [
        'definitions' => [
            'common\repositories\ICategoryRepository' => ['class' => 'common\repositories\CarCategoryRepository'],
            'common\repositories\ICarRepository' => ['class' => 'common\repositories\CarRepository'],
            'common\repositories\IAttachmentRepository' => ['class' => 'common\repositories\AttachmentRepository'],

            'backend\useCase\CarCategory\Add' => ['class' => 'backend\useCase\CarCategory\Add'],
            'backend\useCase\CarCategory\Update' => ['class' => 'backend\useCase\CarCategory\Update'],
        ]
    ]
];
