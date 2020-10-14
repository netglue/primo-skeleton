<?php

declare(strict_types=1);

namespace App\ViewHelper;

use function sprintf;

class GoogleAnalytics
{
    /** @var string|null */
    private $id;

    public function __construct(?string $id = null)
    {
        $this->id = $id;
    }

    public function __invoke(): string
    {
        if (! $this->id) {
            return '';
        }

        return sprintf(
            <<<EOF
            <script>
            (function(i, s, o, g, r, a, m) {
                i['GoogleAnalyticsObject'] = r;
                i[r] = i[r] || function() {
                    (i[r].q = i[r].q || []).push(arguments)
                }, i[r].l = 1 * new Date();
                a = s.createElement(o),
                m = s.getElementsByTagName(o)[0];
                a.async = 1;
                a.src = g;
                m.parentNode.insertBefore(a, m)
            })(window, document, 'script', 'https://www.google-analytics.com/analytics.js', 'ga');
            ga('create', '%s', 'auto');
            ga('send', 'pageview');
            </script>
            EOF,
            $this->id
        );
    }
}
