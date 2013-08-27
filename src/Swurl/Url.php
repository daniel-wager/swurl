<?php
namespace Swurl;

class Url
{
    /**
     * @var Fragment
     */
    private $fragment;

    /**
     * @var Host
     */
    private $host;

    /**
     * @var Scheme
     */
    private $scheme;

    /**
     * @var Query
     */
    private $query;

    /**
     * @var Path
     */
    private $path;

    /**
     * @var AuthInfo
     */
    private $authInfo;

    public function __construct($url = null)
    {
        if ($url) {
            $parts = parse_url($url);

            if ($parts["scheme"]) {
                $this->setScheme(new Scheme($parts["scheme"]));
            }

            if ($parts["user"] || $parts["pass"]) {
                $this->setAuthInfo(new AuthInfo($parts["user"], $parts["pass"]));
            }

            if ($parts["host"]) {
                $this->setHost(new Host($parts["host"]));
            }

            if ($parts["path"]) {
                $this->setPath(new Path($parts["path"]));
            }

            if ($parts["query"]) {
                $this->setQuery(new Query($parts["query"]));
            }

            if ($parts["fragment"]) {
                $this->setFragment(new Fragment($parts["fragment"]));
            }
        }
    }

    public function setPath($path)
    {
        if (is_string($path)) {
            $path = new Path($path);
        }
        $this->path = $path;
    }

    public function setQuery($query)
    {
        if (!($query instanceof Query)) {
            $query = new Query($query);
        }
        $this->query = $query;
    }

    public function setHost($host)
    {
        if (!($host instanceof Host)) {
            $host = new Host($host);
        }
        $this->host = $host;
    }

    public function setAuthInfo($authInfo)
    {
        if (!($authInfo instanceof AuthInfo)) {
            $authInfo = new AuthInfo($authInfo);
        }
        $this->authInfo = $authInfo;
    }

    public function setFragment($fragment)
    {
        if (!($fragment instanceof Fragment)) {
            $fragment = new Fragment($fragment);
        }
        $this->fragment = $fragment;
    }

    public function setScheme($scheme)
    {
        if (!($scheme instanceof Scheme)) {
            $scheme = new Scheme($scheme);
        }
        $this->scheme = $scheme;
    }

    public function equals($url)
    {
        return $this->__toString() == "$url";
    }



    public function __toString()
    {
        $output = "";
        if ($this->scheme) {
            $output .= $this->scheme;
            $output .= "://";
        }

        if ($this->authInfo) {
            $output .= $this->authInfo;
        }

        if ($this->host) {
            $output .= $this->host;
        }

        if ($this->path) {
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

    /**
     * @return \Swurl\AuthInfo
     */
    public function getAuthInfo()
    {
        if (!$this->authInfo) {
            $this->authInfo = new AuthInfo;
        }
        return $this->authInfo;
    }

    /**
     * @return \Swurl\Fragment
     */
    public function getFragment()
    {
        if (!$this->fragment) {
            $this->fragment = new Fragment;
        }
        return $this->fragment;
    }

    /**
     * @return \Swurl\Host
     */
    public function getHost()
    {
        if (!$this->host) {
            $this->host = new Host;
        }
        return $this->host;
    }

    /**
     * @return \Swurl\Path
     */
    public function getPath()
    {
        if (!$this->path) {
            $this->path = new Path;
        }
        return $this->path;
    }

    /**
     * @return \Swurl\Query
     */
    public function getQuery()
    {
        if (!$this->query) {
            $this->query = new Query;
        }
        return $this->query;
    }

    /**
     * @return \Swurl\Scheme
     */
    public function getScheme()
    {
        if (!$this->scheme) {
            $this->scheme = new Scheme;
        }
        return $this->scheme;
    }

    public function setPathAndQuery($pathAndQuery)
    {
        $parts = parse_url($pathAndQuery);
        $this->setPath($parts["path"]);
        $this->setQuery($parts["query"]);
    }

    /**
     * @return Url
     */
    public static function current()
    {
        $url = new self($_SERVER['REQUEST_URI']);
        $url->setHost($_SERVER['HTTP_HOST']);
        if ($_SERVER['HTTPS']) {
            $url->setScheme('https');
        } else {
            $url->setScheme('http');
        }
        return $url;
    }

}