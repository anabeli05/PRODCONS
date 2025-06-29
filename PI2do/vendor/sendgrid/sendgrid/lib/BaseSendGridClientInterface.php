<?php

use SendGrid\Client;
use SendGrid\Mail\Mail;
use SendGrid\Response;

/**
 * This class is the base interface to the Twilio SendGrid Web API.
 *
 * @package SendGrid\Mail
 */
abstract class BaseSendGridClientInterface
{
    /** @var string SendGrid API library version */
    const VERSION = '8.1.2';

    /** @var Client SendGrid HTTP Client library */
    public $client;

    /** @var string SendGrid version */
    public $version = self::VERSION;

    /** @var allowedRegionsHostMap regions specific hosts */
    public $allowedRegionsHostMap = [
        "eu" => "https://api.eu.sendgrid.com",
        "global" => "https://api.sendgrid.com",
    ];

    /**
     * Set up the HTTP Client.
     *
     * @param string $auth Authorization header value.
     * @param string $host Default host/base URL for the client.
     * @param array $options An array of options, currently only "host", "curl",
     *                       "version", "verify_ssl", and "impersonateSubuser",
     *                       are implemented.
     */
    public function __construct($auth, $host, $options = array())
    {
        $headers = [
            $auth,
            'User-Agent: sendgrid/' . $this->version . ';php',
            'Accept: application/json',
        ];

        $host = isset($options['host']) ? $options['host'] : $host;

        $version = isset($options['version']) ? $options['version'] : '/v3';

        if (!empty($options['impersonateSubuser'])) {
            $headers[] = 'On-Behalf-Of: ' . $options['impersonateSubuser'];
        }

        $this->client = new Client($host, $headers, $version);

        $this->client->setCurlOptions(isset($options['curl']) ? $options['curl'] : []);
        $this->client->setVerifySSLCerts(isset($options['verify_ssl']) ? $options['verify_ssl'] : true);
    }

    /**
     * Make an API request.
     *
     * @param Mail $email A Mail object, containing the request object
     *
     * @return Response
     */
    public function send(Mail $email)
    {
        return $this->client->mail()->send()->post($email);
    }

    /*
      * Client libraries contain setters for specifying region/edge.
      * This allows support global and eu regions only. This set will likely expand in the future.
      * Global should be the default
      * Global region means the message should be sent through:
      * HTTP: api.sendgrid.com
      * EU region means the message should be sent through:
      * HTTP: api.eu.sendgrid.com
    */
    public function setDataResidency($region): void
    {
        if (array_key_exists($region, $this->allowedRegionsHostMap)) {
            $this->client->setHost($this->allowedRegionsHostMap[$region]);
        } else {
            throw new InvalidArgumentException("region can only be \"eu\" or \"global\"");
        }
    }

}
