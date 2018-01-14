<?php

namespace AppBundle\Service\LxdApi;


use AppBundle\Entity\Host;
use AppBundle\Service\LxdApi\Util\HttpHelper;
use Httpful\Request;

class ImageApi extends HttpHelper
{

    protected function getEndpoint($urlParam = NULL)
    {
        return 'images';
    }

    /**
     * ImageApi constructor.
     * @param $cert_location
     * @param $cert_key_location
     * @param $cert_passphrase
     * @throws \AppBundle\Exception\WrongInputException
     */
    public function __construct($cert_location, $cert_key_location, $cert_passphrase)
    {
        parent::__construct($cert_location, $cert_key_location, $cert_passphrase);
        $this->init();
    }


    /**
     * @param Host $host
     * @param $body
     * @return \Httpful\Response
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    public function createImage(Host $host, $body){
        $uri = $this->buildUri($host, $this->getEndpoint());
        return Request::post($uri)
            -> body($body)
            -> send();
    }

    /**
     * @param Host $host
     * @param $operationsId
     * @return \Httpful\Response
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    public function getOperationsLink(Host $host, $operationsId){
        $uri = $this->buildUri($host, 'operations/'.$operationsId);
        return Request::get($uri)
            -> send();
    }

    /**
     * @param Host $host
     * @param $operationsId
     * @return \Httpful\Response
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    public function getOperationsLinkWithWait(Host $host, $operationsId){
        $uri = $this->buildUri($host, 'operations/'.$operationsId.'/wait');
        return Request::get($uri)
            -> send();
    }

    /**
     * @param Host $host
     * @param String $fingerprint
     * @return \Httpful\Response
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    public function getImageByFingerprint(Host $host, String $fingerprint){
        $uri = $this->buildUri($host, $this->getEndpoint().'/'.$fingerprint);
        return Request::get($uri)
            -> send();
    }

    /**
     * @param Host $host
     * @param String $fingerprint
     * @return \Httpful\Response
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    public function removeImageByFingerprint(Host $host, String $fingerprint){
        $uri = $this->buildUri($host, $this->getEndpoint().'/'.$fingerprint);
        return Request::delete($uri)
            -> send();
    }

    /**
     * @param Host $host
     * @param String $fingerprint
     * @param $body
     * @return \Httpful\Response
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    public function putImageUpdate(Host $host, String $fingerprint, $body){
        $uri = $this->buildUri($host, $this->getEndpoint().'/'.$fingerprint);
        return Request::put($uri)
            -> body($body)
            -> send();
    }

}