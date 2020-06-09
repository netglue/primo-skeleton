<?php
declare(strict_types=1);

return [
    'prismic' => [
        'api' => null, // <- Nothing is gonna work without the API URL
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
    ],
];
