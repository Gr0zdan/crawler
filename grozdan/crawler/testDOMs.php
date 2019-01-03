<?php
// needto do it in class
set_time_limit(360);

function getTitles(&$links, &$titles){
    // $tag = 'title';
    // $html = file_get_contents($href);
    // $dom = new DOMDocument();
    // @$dom->loadHTML($html);
    // $titles[] = $dom->getElementsByTagName($tag)->item(0)->textContent;
    foreach ($links as $link) {
        $link = str_replace('\\"', '', $link);
        $link = str_replace('"', '', $link);
        $titles[] = $link;
    }
}

function getHrefs($link, $tag, &$links){
    $html = file_get_contents($link);//source
    $dom = new DOMDocument();
    @$dom->loadHTML($html);
    $titles = [];
    $results_of_a = $dom->getElementsByTagName($tag);//tag_results
	foreach($results_of_a as $tagName/*current_tag*/) {
        $href = $tagName->getAttribute("href");
        $scheme = parse_url($link)["scheme"];
        $host = parse_url($link)["host"];
        $path = parse_url($link)["path"];
        if(substr($href, 0, 1) == "<"){
            continue;
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
            continue;
        } elseif (substr($href,0, 5) != "https" && substr($href,0, 4) != "http") {
            $href = $scheme."://".$host."/".$href;
        } elseif($href === ''){
            continue;
        } else{
            $href = $href;
        }
        if(!in_array($href, $links)){
            $links[] = $href;
        }
    }
    getTitles($links, $titles);
    $titledLinks = array_combine($titles, $links);
    return $titledLinks;
}

if(isset($_GET["link"])){
	$link = $_GET["link"];
	$tag = "a";
}else{
	$link = $_POST["link"];
	$tag = $_POST["tag"];// need to add operations for different tags
}
$links = file_get_contents("links/links.txt");
$links = explode("\n", $links);
$titledLinks = getHrefs($link, $tag, $links);
$saveLinks = fopen("links/links.txt", "w");
foreach($titledLinks as $title => $link){
    $link = str_replace('\\"/', '', $link);
    $link = str_replace('\\"', '', $link);
    $link = str_replace('"', '', $link);
    echo '<a href="http://localhost/grozdan/crawler/testDOMs.php?link='.$link.'" target="_blank" title="'.$link.'">'.$title.'</a><hr>';
    $line = $link."\n";
    fwrite($saveLinks, $line);
}
