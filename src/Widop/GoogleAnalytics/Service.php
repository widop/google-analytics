<?php

/*
 * This file is part of the Wid'op package.
 *
 * (c) Wid'op <contact@widop.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Widop\GoogleAnalytics;

/**
 * Google Analytics service.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class Service
{
    /** @var \Widop\GoogleAnalytics\Client */
    protected $client;

    /**
     * Google analytics service constructor.
     *
     * @param \Widop\GoogleAnalytics\Client $client The google analytics client.
     */
    public function __construct(Client $client)
    {
        $this->setClient($client);
    }

    /**
     * Gets the google analytics client.
     *
     * @return \Widop\GoogleAnalytics\Client The google analytics client.
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Sets the google analytics client.
     *
     * @param \Widop\GoogleAnalytics\Client $client The google analytics client.
     *
     * @return \Widop\GoogleAnalytics\Service The google analytics service.
     */
    public function setClient(Client $client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Queries the google analytics service.
     *
     * @param \Widop\GoogleAnalytics\Query $query The google analytics query.
     *
     * @throws \Widop\GoogleAnalytics\GoogleAnalyticsException If an error occured when querying the google analytics service.
     *
     * @return \Widop\GoogleAnalytics\Response The google analytics response.
     */
    public function query(Query $query)
    {
        $accessToken = $this->getClient()->getAccessToken();
        $uri = $query->build($accessToken);
        $content = $this->getClient()->getHttpAdapter()->getContent($uri);
        $json = json_decode($content, true);

        if (!is_array($json) || isset($json['error'])) {
            throw GoogleAnalyticsException::invalidQuery(isset($json['error']) ? $json['error']['message'] : 'Invalid json');
        }

        return new Response($json);
    }
}
