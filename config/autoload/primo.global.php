<?php
declare(strict_types=1);

use App\Content\Container\SingleDocumentLocatorStaticFactory;
use App\ViewHelper\Container\SingleDocumentStaticFactory;
use Prismic\Predicate;

return [
    'prismic' => [
        'api' => 'https://primo.cdn.prismic.io/api/v2', // <- Nothing is gonna work without the API URL. Change it to your own repo.
        'token' => null, // <- And you must enter a token if required by your setup
    ],
    'primo' => [
        'previews' => [
            // The URL to redirect to when a preview does not specify a document
            'defaultUrl' => '/',
            // You can change this to any url you please. Some obscurity is not a bad idea.
            'previewUrl' => Primo\ConfigProvider::DEFAULT_PREVIEW_URL,
        ],
        'webhook' => [
            // The secret you expect in the webhook payload. Ideally, this would be set to something very random
            'secret' => null,
            // Webhooks are disabled by default to encourage the configuration of a secret
            'enabled' => false,
            // The URL that webhooks should be posted to. Again, this could be obscure to improve security.
            'url' => Primo\ConfigProvider::DEFAULT_WEBHOOK_URL,
        ],
        'notFound' => [
            'finder' => 'document.404',
        ],
        'error' => [
            'default' => 'document.500',
        ],
        'documents' => [
            'document.404' => [
                'predicates' => [
                    Predicate::at('document.type', 'error'),
                    Predicate::at('my.error.errorCode', 404),
                ],
            ],
            'document.500' => [
                'predicates' => [
                    Predicate::at('document.type', 'error'),
                    Predicate::at('my.error.errorCode', 500),
                ],
            ],
            'mainMenu' => [
                'type' => 'link-list',
                'uid' => 'main',
            ],
        ],
    ],
    'dependencies' => [
        'factories' => [
            'document.404' => [SingleDocumentLocatorStaticFactory::class, 'document.404'],
            'document.500' => [SingleDocumentLocatorStaticFactory::class, 'document.500'],
            'mainMenu' => [SingleDocumentLocatorStaticFactory::class, 'mainMenu'],
        ],
    ],
    'view_helpers' => [
        'factories' => [
            'mainMenu' => [SingleDocumentStaticFactory::class, 'mainMenu'],
        ],
    ],
];
