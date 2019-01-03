<head>
    <link rel="stylesheet" href="styles/removeCssFromLinks.css">
</head>
<body>
<?php

set_time_limit(360);

class Crawler {

	private $start_link     = "";
	private $tag_name	      = "";
	private $source         = "";
	private $dom	          = "";
	private $current_tag    = "";
	private $tag_results    = [];
	private $processed_href = "";
  private $context_opts   = "";
  private $context_header = "";
	private $delimeter      = "-----";

	public function __construct ($startLink, $tagName) {
		$this->start_link = $startLink;
		$this->tag_name   = $tagName;
	}

  private function setContextOpts () {
    $this->context_opts = [
      "http" => [
        "method" => "GET",
        "header" => "Accept-language: en\r\n" .
          "Cookie: foo=bar\r\n"
      ]
    ];
	}

  private function setContextHeader () {
    $this->context_header = stream_context_create($this->context_opts);
	}

	private function getSource () {
    $this->setContextOpts();
    $this->setContextHeader();
    $this->source = file_get_contents($this->start_link, false, $this->context_header);
	}

	private function loadDom () {
		$this->dom = new DOMDocument();
		@$this->dom->loadHTML($this->source);
	}

	private function getTags () {
		$this->tag_results = $this->dom->getElementsByTagName($this->tag_name);
	}

	private function setCurrentTag ($current_tag) {
		$this->current_tag = $current_tag;
	}

	private function processHref () {
		$href   = $this->current_tag->getAttribute("href");
		$scheme = parse_url($this->start_link)["scheme"];
		$host   = parse_url($this->start_link)["host"];
		$path   = parse_url($this->start_link)["path"];
		if(substr($href, 0, 1) == "<"){
				return NULL;
		} elseif (substr($href, 0, 1) == "/" && substr($href, 0, 2) != "//") {
				$href = $scheme."://".$host.$href;
		} elseif (substr($href, 0, 2) == "//") {
				$href = $scheme.":".$href;
		} elseif (substr($href, 0, 2) == "./") {
				$href = $scheme."://".$host.dirname($path).substr($href,1);
		} elseif (substr($href, 0, 1) == "#") {
				$href = $scheme."://".$host.$path.$href;
		} elseif (substr($href, 0, 3) == "../") {
				$href = $scheme."://".$host."/".$href;
		} elseif (substr($href, 0, 11) == "javascript:") {
				return NULL;
		} elseif (substr($href,0, 5) != "https" && substr($href,0, 4) != "http") {
				$href = $scheme."://".$host."/".$href;
		} elseif($href === ''){
				return NULL;
		} else{
				$href = $href;
		}

		$href = str_replace('\\"/', '', $href);
		$href = str_replace('\\"', '', $href);
		$href = str_replace('"', '', $href);

		return $href;
	}

	private function getProcessedHref () {
		$this->processed_href = $this->processHref();
	}

  public function crawl (&$links) {
		$this->getSource();
		$this->loadDom();
		$this->getTags();
		$br = 0;
		while($br < $this->tag_results->length){
			$this->setCurrentTag($this->tag_results[$br]);
			$this->getProcessedHref();
			if(!in_array($this->processed_href, $links) and $this->processed_href !== NULL){
				$links[] = $this->processed_href;
			}
			++$br;
		}

		$links[] = $this->delimeter;
	}

}

$links  = [];
$link   = "https://www.amazon.co.uk/";
$tag    = "a";
$spider = new Crawler($link, $tag);
$spider->crawl($links);
// $levels = explode("-----", implode("\n",$links)); /* need to write to file and then read and then iterate every level and then write to file */
while(count($links)){
  echo "<div><a". /*href=\"$links[0]\" target=\"_blank\" */ ">".$links[0]."</a><hr></div>";
  $links = array_slice($links, 1);
}

?>
    <script src="js/visited.js"></script>
</body>

<!--
    https://stackoverflow.com/questions/2107759/php-file-get-contents-and-setting-request-headers/2107792#2107792
    https://stackoverflow.com/questions/34450193/file-get-contents-returns-nothing-on-html-input#
-->
