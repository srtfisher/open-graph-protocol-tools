<?php namespace OpenGraph;

/**
 * OpenGraph Fetcher
 *
 * Request information from a remote page to pull in Open Graph
 * information.
 *
 * @package opengraph
 */
class Fetcher {
    /**
     * @var string
     */
    protected $url;

    /**
     * @var array
     */
    protected $options;

    /**
     * {@inheritdoc}
     * 
     * @param string URL to pull from
     * @param array Options to handle the request
     */
    public function __construct($url, $options = [])
    {
        $this->url = $url;
        $this->options = $options;
    }
}