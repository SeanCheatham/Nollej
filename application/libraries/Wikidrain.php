
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * wikidrain: A simple PHP wrapper for the WikiMedia API centralized around querying Wikipedia articles
 *
 * @package    wikidrain
 * @copyright  Copyright (c) 2013-2014 Andrew Breksa &lt;abreksa4@gmail.com>
 * @license    https://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt  LGPL License
 * @version    Release: @1.0@
 * @link       https://github.com/abreksa4/wikidrain
 * @since      Class available since Release 1.0
 */
class Wikidrain
{
    protected $_apiURL = 'http://en.wikipedia.org/w/api.php?format=xml&amp;';
    protected $_apiParams = array();
    protected $_query;
    protected $_userAgent = "nollej/1.0 (http://seancheatham.com/nollej)";
    //XML vars
    protected $_XML;
    protected $_data = array();
    protected $_count = 0;
    protected $_deadSections = array('Notes', 'Further reading', 'See also', 'References', 'External links');
    //Shared vars
    protected $_string;
    protected $_json;

    /*function __construct($userAgent)
    {
        $this->_userAgent = $userAgent;
    }*/

    function __destruct()
    {
        $this->release();
    }

    /**
     *
     * Performs a wikipedia search for the supplied query, returns the results
     *
     * @param $query string
     * @param $numResult int
     * @return array|mixed
     */
    public function Search($query, $numResult)
    {
        $this->_query = htmlspecialchars($query);
        $this->_apiParams['action'] = 'opensearch';
        $this->_apiParams['params'] = array(
            "limit={$numResult}",
            "search={$this->_query}",
            "suggest=false",
        );
        $result = $this->callApi();
        $result = $this->parseSearch($result);
        $this->release();
        return $result;
    }

    /**
     *
     * Returns a multidimensional array containing the TOC of the supplied page
     *
     * @param $title string
     * @return array|mixed
     */
    public function getSections($title)
    {
        $this->_apiParams['action'] = 'parse';
        $this->_apiParams['params'] = array(
            "prop=sections",
            "page={$this->prepTitle($title)}",
            "redirects=true",
        );
        $result = $this->callApi();
        //$result = $this->parseSections($result);
        $this->release();
        return $result;
    }

    /**
     *
     * Reruns parsed page text
     *
     * @param $title string
     * @param $section int
     * @return mixed|string
     */
    public function getText($title, $section)
    {
        $this->_apiParams['action'] = 'query';
        $this->_apiParams['params'] = array(
            "prop=revisions",
            "titles={$this->prepTitle($title)}",
            "redirects=true",
            "rvprop=content",
            "rvsection={$section}",
        );
        $result = $this->callApi();
        $result = $this->parseText($result, $section);
        $this->release();
        return $result;
    }

    /**
     *
     * Returns an array of related pages to the supplied page
     *
     * @param $title string
     * @return array|mixed|string
     */
    public function getRelated($title)
    {
        $title = $this->prepTitle($title);
        $this->_data = $this->getSections($title);
        $this->_string = count($this->_data) + 1;
        $result = $this->getText($title, $this->_string);
        $result = preg_replace('/==(.*?)\==/s', '', $result);
        $result = str_replace("\r\n", "\n", $result);
        $result = str_replace("*", '', $result);
        $result = explode("\n", $result);
        $result = array_filter(array_map('trim', $result));
        $this->release();
        return $result;

    }
	
	public function getLinks($title)
	{
		$this->_apiParams['action'] = 'query';
        $this->_apiParams['params'] = array(
            "prop=links",
            "titles=Dog",
            "redirects=true",
        );
        $result = $this->callApi();
        //$result = $this->parseText($result, $section);
        $this->release();
        return $result;
	}

    /**
     * Releases the class properties to prevent the "complex types" error
     */
    private function release()
    {
        $this->_apiParams = NULL;
        $this->_query = NULL;
        //XML vars
        $this->_XML = NULL;
        $this->_data = NULL;
        //Shared
        $this->_string = NULL;
    }

    /**
     *
     * Preps titles for use
     *
     * @param $string string
     * @return mixed
     */
    private function prepTitle($string)
    {
        $string = str_replace(' ', '_', $string);
        $string = htmlspecialchars($string);
        return $string;
    }

    /**
     *
     * Calls the wikmedia API
     *
     * @return mixed
     */
    private function callApi()
    {
        $params = implode('&amp;', $this->_apiParams['params']);
        $url = "{$this->_apiURL}action={$this->_apiParams['action']}&amp;{$params}";
		$url = "http://en.wikipedia.org/w/api.php?action=query&prop=links&format=json&titles=Dog&redirects=";
		//die($url);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_USERAGENT, $this->_userAgent);
        $result = curl_exec($curl);
        return $result;
    }

    //XML parsing methods

    /**
     *
     * Parses the Search results
     *
     * @param $xml mixed
     * @return array
     */
    private function parseSearch($xml)
    {
        $this->_count = 0;
        $this->_XML = new SimpleXMLElement($xml);
        foreach ($this->_XML->Section->Item as $item) {
            $this->_data[$this->_count] = array(
                "title" => "$item->Text",
                "description" => "$item->Description",
            );
            $this->_count++;
        }
        return $this->_data;
    }

    /**
     *
     * Parses the getSections results
     *
     * @param $xml mixed
     * @return array
     */
    private function parseSections($xml)
    {
        // TODO: Possibly return as multidimensional array, using index/position as markers...
        $this->_count = 0;
        $this->_XML = new SimpleXMLElement($xml);
        foreach ($this->_XML->parse->sections->s as $section) {
            if (!in_array($section['line'], $this->_deadSections)) {
                $this->_data[$this->_count] = array(
                    'title' => "{$section['line']}",
                    'index' => "{$section['index']}",
                    'position' => "{$section['number']}"
                );
                $this->_count++;
            }
        }
        return $this->_data;
    }

    /**
     *
     * Parses the getText results
     *
     * @param $xml mixed
     * @param $section int
     * @return mixed|string
     */
    private function parseText($xml, $section)
    {
        $this->_XML = new SimpleXMLElement($xml);
        $this->_data = $this->_XML->query->pages->page->revisions->rev;
        $string = $this->_data[0];

        if ($section == 0) {
            $string = strstr($string, '\'\'\''); //This removes the images/info box if the section is the summary
            $string = str_replace('\'\'\'', '"', $string); //Replaces the ''' around titles to be "
        }
        $string = preg_replace('/&lt;ref[^>]*>[^&lt;]+&lt;\/ref[^>]*>|\{{(?>[^}]++|}(?!}))\}}|==*[^=]+=*\n|File:(.*?)\n|\[\[|\]]|\n/', '', $string); //Compliments of Jerry [http://unknownoo8.deviantart.com/]
        //|\s{2,}
        $string = str_replace('|', '/', $string); //Makes the wikilinks look better
        $string = strip_tags($string); //Just in case
        return $string;
    }
}
?>