<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Wikipedia
{
    protected $_apiURL = 'http://en.wikipedia.org/w/api.php?format=json&';
    protected $_apiParams = array();
    protected $_query;
    protected $_userAgent = "nollej/1.0 (http://seancheatham.com/nollej)";
	protected $_limit = 1;
    protected $_continue = "";
	
	public function getLinksByTitle($title)
	{
		$this->_apiParams['action'] = 'query';
        $this->_apiParams['params'] = array(
			"generator=links",
            "prop=info",
            "titles={$title}",
            "redirects=true",
			"gpllimit=500",
        );
        $result = $this->callApi();
        return $result;
	}
	
	public function getLinksById($id)
	{
		$this->_apiParams['action'] = 'query';
        $this->_apiParams['params'] = array(
			"generator=links",
            "prop=info",
            "pageids={$id}",
            "redirects=true",
			"gpllimit=500",
        );
        $result = $this->callApi();
        return $result;
	}
	
	public function getPageParseByTitle($title)
	{
		$this->_apiParams['action'] = 'parse';
        $this->_apiParams['params'] = array(
            "prop=text",
			"section=0",
            "page={$title}",
            "redirects=true",
        );
        $result = $this->callApi();
        return $result;
	}
	
	public function getPageByTitle($title)
	{
		//$title = urlencode($title);
		$this->_apiParams['action'] = 'query';
        $this->_apiParams['params'] = array(
            "prop=revisions",
			"rvprop=content",
            "titles={$title}",
            "redirects=true",
        );
        $result = $this->callApi();
        return $result;
	}
	
	private function callApi()
    {
        $params = implode('&', $this->_apiParams['params']);
        $url = "{$this->_apiURL}action={$this->_apiParams['action']}&continue={$this->_continue}&{$params}";
		//die($url);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_USERAGENT, $this->_userAgent);
        $result = curl_exec($curl);
        return json_decode($result, $assoc=true);
    }
	
}