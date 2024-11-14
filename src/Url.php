<?php

namespace Swurl;

class Url
{
    private ?Fragment $fragment = null;

    private ?Host $host = null;

    private ?Scheme $scheme = null;

    private ?Query $query = null;

    private ?Path $path = null;

    private ?AuthInfo $authInfo = null;

    private bool $isSchemeless = false;

    public function __construct(?string $url = null)
    {
        if ($url) {

            $parts = parse_url($url);

            if (isset($parts['scheme'])) {
                $this->setScheme(new Scheme($parts['scheme']));
            } elseif (substr($url, 0, 2) === '//') {
                $this->makeSchemeless();
            }

            if (isset($parts['user']) || isset($parts['pass'])) {
                $this->setAuthInfo(new AuthInfo($parts['user'], $parts['pass']));
            }

            if (isset($parts['host'])) {
                $this->setHost(new Host($parts['host']));
                if (isset($parts['port'])) {
                    $this->host->setPort($parts['port']);
                }
            }

            if (isset($parts['path'])) {
                $this->setPath(new Path($parts['path']));
            }

            if (isset($parts['query'])) {
                $this->setQuery(new Query($parts['query']));
            }

            if (isset($parts['fragment'])) {
                $this->setFragment(new Fragment($parts['fragment']));
            }
        }
    }

    public function makeSchemeless(): void
    {
        $this->isSchemeless = true;
    }

    public function isSchemeless(): bool
    {
        return $this->isSchemeless;
    }

    public function setPath(?Path $path): static
    {
        $this->path = $path;

        return $this;
    }

    public function setQuery(?Query $query): static
    {
        $this->query = $query;

        return $this;
    }

    public function setHost(?Host $host): static
    {
        $this->host = $host;

        return $this;
    }

    public function setAuthInfo(?AuthInfo $authInfo): static
    {
        $this->authInfo = $authInfo;

        return $this;
    }

    public function setFragment(?Fragment $fragment)
    {
        $this->fragment = $fragment;

        return $this;
    }

    public function setScheme(?Scheme $scheme): static
    {
        $this->scheme = $scheme;
        $this->isSchemeless = is_null($scheme) || empty((string) $scheme);

        return $this;
    }

    public function equals($url): bool
    {
        return $this->__toString() == "$url";
    }

    public function setEncoder(?string $encoder): static
    {
        $this->query->setEncoder($encoder);
        $this->path->setEncoder($encoder);

        return $this;
    }

    public function __toString(): string
    {
        $output = '';

        if ($this->host) {
            if ($this->isSchemeless) {
                $output .= '//';
            } elseif ($this->scheme) {
                $output .= $this->scheme;
                $output .= '://';
            }
        }

        if ($this->authInfo) {
            $output .= $this->authInfo;
        }

        if ($this->host) {
            $output .= $this->host;
        }

        if ($this->path) {
            if ($this->host) {
                if (! $this->path->hasLeadingSlash()) {
                    $output .= '/';
                }
            }
            $output .= $this->path;
        }

        if ($this->query) {
            $output .= $this->query;
        }

        if ($this->fragment) {
            $output .= $this->fragment;
        }

        return $output;
    }

    public function __clone()
    {
        if ($this->scheme) {
            $this->scheme = clone $this->scheme;
        }

        if ($this->authInfo) {
            $this->authInfo = clone $this->authInfo;
        }

        if ($this->host) {
            $this->host = clone $this->host;
        }

        if ($this->path) {
            $this->path = clone $this->path;
        }

        if ($this->query) {
            $this->query = clone $this->query;
        }

        if ($this->fragment) {
            $this->fragment = clone $this->fragment;
        }
    }

    public function getAuthInfo(): AuthInfo
    {
        if (! $this->authInfo) {
            $this->authInfo = new AuthInfo;
        }

        return $this->authInfo;
    }

    public function getFragment(): Fragment
    {
        if (! $this->fragment) {
            $this->fragment = new Fragment;
        }

        return $this->fragment;
    }

    public function getHost(): Host
    {
        if (! $this->host) {
            $this->host = new Host;
        }

        return $this->host;
    }

    public function getPath(): Path
    {
        if (! $this->path) {
            $this->path = new Path;
        }

        return $this->path;
    }

    public function getQuery(): Query
    {
        if (! $this->query) {
            $this->query = new Query;
        }

        return $this->query;
    }

    public function getScheme(): Scheme
    {
        if (! $this->scheme) {
            $this->scheme = new Scheme;
        }

        return $this->scheme;
    }

    public function setUri(string $uri): static
    {
        $parts = parse_url($uri);
        if ($parts['path']) {
            $this->setPath(new Path($parts['path']));
        }
        if ($parts['query']) {
            $this->setQuery(new Query($parts['query']));
        }
        if ($parts['fragment']) {
            $this->setFragment(new Fragment($parts['fragment']));
        }

        return $this;
    }

    public static function current(): Url
    {
        $url = new self($_SERVER['REQUEST_URI']);
        $url->setHost(new Host($_SERVER['HTTP_HOST']));
        if (isset($_SERVER['HTTPS'])) {
            $url->setScheme(new Scheme('https'));
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
            $url->setScheme(new Scheme('https'));
        } else {
            $url->setScheme(new Scheme('http'));
        }

        return $url;
    }
}
