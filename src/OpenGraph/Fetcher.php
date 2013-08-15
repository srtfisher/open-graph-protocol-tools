<?php namespace OpenGraph;

use Guzzle\Http\Client;

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
     * @var string
     */
    protected $response;

    /**
     * @var array
     */
    protected $keys = [];

    /**
     * {@inheritdoc}
     * 
     * @param string URL to pull from
     * @param array Options to handle the request (headers pased to Guzzle)
     */
    public function __construct($url, $options = [])
    {
        $this->url = $url;
        $this->options = $options;

        $this->fetch();
    }

    /**
     * Fetch the Current URL
     * 
     * @return void
     * @access protected
     * @throws \Exception
     */
    protected function fetch()
    {
        $client = new Client();
        $request = $client->get($this->url, $this->options);

        $this->response = $request->send();

        if ($this->response->isError())
            throw new \Exception($this->response, $this->response->code);

        $this->parse();
    }

    /**
     * Parse the Current Response
     * 
     * @return void
     * @access protected
     */
    protected function parse()
    {
        $this->keys = [];
        $old_libxml_error = libxml_use_internal_errors(true);

        $doc = new \DOMDocument();
        $doc->loadHTML($this->response->getBody(true));
        
        libxml_use_internal_errors($old_libxml_error);

        $tags = $doc->getElementsByTagName('meta');

        if (! $tags OR $tags->length === 0)
            return FALSE;

        $nonOgDescription = null;

        foreach ($tags AS $tag) :
            if ($tag->hasAttribute('property') &&
                strpos($tag->getAttribute('property'), 'og:') === 0) {
                $key = strtr(substr($tag->getAttribute('property'), 3), '-', '_');
                $this->keys[$key] = $tag->getAttribute('content');
            }
            
            //Added this if loop to retrieve description values from sites like the New York Times who have malformed it. 
            if ($tag ->hasAttribute('value') && $tag->hasAttribute('property') &&
                strpos($tag->getAttribute('property'), 'og:') === 0) {
                $key = strtr(substr($tag->getAttribute('property'), 3), '-', '_');
                $this->keys[$key] = $tag->getAttribute('value');
            }
            //Based on modifications at https://github.com/bashofmann/opengraph/blob/master/src/OpenGraph/OpenGraph.php
            if ($tag->hasAttribute('name') && $tag->getAttribute('name') === 'description') {
                $nonOgDescription = $tag->getAttribute('content');
            }
        endforeach;

        //Based on modifications at https://github.com/bashofmann/opengraph/blob/master/src/OpenGraph/OpenGraph.php
        if (! isset($this->keys['title'])) {
            $titles = $doc->getElementsByTagName('title');
            if ($titles->length > 0) {
                $this->keys['title'] = $titles->item(0)->textContent;
            }
        }
        if (! isset($this->keys['description']) AND $nonOgDescription)
            $this->keys['description'] = $nonOgDescription;

        //Fallback to use image_src if ogp::image isn't set.
        if (! isset($page->values['image'])) {
            $domxpath = new\DOMXPath($doc);
            $elements = $domxpath->query("//link[@rel='image_src']");

            if ($elements->length > 0) {
                $domattr = $elements->item(0)->attributes->getNamedItem('href');

                if ($domattr) {
                    $this->keys['image'] = $domattr->value;
                    $this->keys['image_src'] = $domattr->value;
                }
            }
        }

        if (empty($this->keys)) return false;
    }

    /**
     * Get the Keys after getting this whole application
     *
     * @return array
     */
    public function getKeys() { return $this->keys; }
}