<?php
/**
 * Created by Malt Blue <http://www.maltblue.com>.
 * User: matthewsetter
 * Date: 04/05/2013
 * Time: 15:27
 * 
 */

namespace GuzzleApp;

use Guzzle\Http\Client;
use Guzzle\Cache\ZendCacheAdapter;
use Doctrine\Common\Cache\ArrayCache;
use Guzzle\Cache\DoctrineCacheAdapter;
use Guzzle\Plugin\Cache\CachePlugin;
use Guzzle\Plugin\History\HistoryPlugin;

class ApiCaller
{
    const DEFAULT_URL = 'http://httpbin.org';
    const DEFAULT_HISTORY_TAIL = 5;
    const DEFAULT_CACHE_TTL = 3600;
    const DEFAULT_CACHE_REVALIDATE = 'never';

    protected $_client;
    protected $_response;
    protected $_request;
    protected $_history;

    public function __construct()
    {
        $this->_client = new Client(self::DEFAULT_URL, array(
            'params.cache.override_ttl' => self::DEFAULT_CACHE_TTL,
            'params.cache.revalidate'   => self::DEFAULT_CACHE_REVALIDATE
        ));

        $this->_initialiseHistory()
            ->_initialiseCache()
            ->_initialiseLogger();
    }

    protected function _initialiseCache()
    {
        $backend = new ArrayCache();
        $adapter = new DoctrineCacheAdapter($backend);
        $cache = new CachePlugin($adapter);
        $this->_client->addSubscriber($cache);

        return $this;
    }

    protected function _initialiseHistory()
    {
        $history = new HistoryPlugin();
        $history->setLimit(self::DEFAULT_HISTORY_TAIL);
        $this->_client->addSubscriber($history);
        $this->_history = $history;

        return $this;
    }

    protected function _initialiseLogger()
    {
        $this->_client->addSubscriber(new \GuzzleApp\Plugin\Log());

        return $this;
    }

    public function printRequestInformation()
    {
        printf(
            "encoding: %s\nlength: %s\nstatus code: %s\nfreshness: %s\netag: %s\n",
            $this->_response->getContentEncoding(),
            $this->_response->getContentLength(),
            $this->_response->getStatusCode(),
            $this->_response->getFreshness(),
            $this->_response->getEtag()
        );
    }

    public function getContent()
    {
        $this->_response = $this->_client->get('/html')->send();

        return $this;
    }

    public function getRequestBody()
    {
        return $this->_response->getBody();
    }

    public function sendPostRequest(array $params = array())
    {
        $this->_response = $this->_client
                                ->post("/post");

        if (!empty($params)) {
            $this->_response->addPostFields($params);
        }

        $this->_response->send();

        return $this;
    }

    public function getHeaderInformation($headerName = null)
    {
        if (!is_null($headerName)) {
            return $this->_response->getHeader($headerName);
        }
        return $this->_response->getHeaders();
    }

    public function getLastRequest()
    {
        print $this->_history->getLastRequest();
    }
}