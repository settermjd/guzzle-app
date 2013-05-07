<?php
/**
 * Created by Malt Blue <http://www.maltblue.com>.
 * User: matthewsetter
 * Date: 04/05/2013
 * Time: 16:04
 * 
 */

namespace GuzzleApp\Plugin;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Guzzle\Common\Event;
use Guzzle\Log\Zf2LogAdapter;
use Guzzle\Plugin\Log\LogPlugin;
use Guzzle\Log\MessageFormatter;
use Zend\Log\Logger;
use Zend\Log\Writer\Stream;

class Log implements EventSubscriberInterface
{
    protected $_logger;

    public function __construct()
    {
        $this->_logger = new Logger;
        $writer = new Stream('log/output.log');
        $this->_logger->addWriter($writer);
    }

    public static function getSubscribedEvents()
    {
        return array(
            'request.before_send' => 'onBeforeSend'
        );
    }

    public function onBeforeSend(Event $event)
    {
        $this->_logger->info('About to send a request: ' . $event->getName());
    }
}