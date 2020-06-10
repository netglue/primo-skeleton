<?php
declare(strict_types=1);

namespace App\ViewHelper;

use Psr\Http\Message\UriInterface;

use function str_replace;

class RepositoryInformation
{
    /** @var UriInterface */
    private $repoUri;

    public function __construct(UriInterface $uri)
    {
        $this->repoUri = $uri;
    }

    public function __invoke() : self
    {
        return $this;
    }

    /** Full url to the api endpoint */
    public function url() : string
    {
        return (string) $this->repoUri;
    }

    /** Api host name */
    public function host() : string
    {
        return $this->stripCdnFromHost();
    }

    private function stripCdnFromHost() : string
    {
        $host = $this->repoUri->getHost();

        return str_replace('.cdn.prismic', '.prismic', $host);
    }
}
