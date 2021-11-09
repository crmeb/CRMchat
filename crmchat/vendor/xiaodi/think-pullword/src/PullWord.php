<?php

namespace PullWord;

use PullWord\Service\Classify;
use PullWord\Service\Pull;


class PullWord
{
    protected $namespace = "\\PullWord\\Service\\";

    protected $source;

    public function __construct($source = '')
    {
        if ($source) {
            $this->source = $source;
        }
    }

    public function service($service, $source = '')
    {
        if ($source) {
            $this->source = $source;
        }

        if (class_exists($service)) {
            $service = new $service($this->source);
        } else {
            $class = $this->namespace . ucfirst($service);
            $service = new $class($this->source);
        }

        return $service;
    }

    public function pull($source = '')
    {
        return $this->service(Pull::class, $source);
    }

    public function classify($source = '')
    {
        return $this->service(Classify::class, $source);
    }
}
