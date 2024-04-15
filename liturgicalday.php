<?php

 if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')   
         $url = "https://";   
    else  
         $url = "http://";   
    // Append the host(domain name, ip) to the URL.   
    $url.= $_SERVER['HTTP_HOST'];   
    
    // Append the requested resource location to the URL   
    $url.= $_SERVER['REQUEST_URI'];    
      
    echo $url;  

$query = parse_url($url, PHP_URL_QUERY);
parse_str($query, $queryParams);
print_r($queryParams);

$lang = "ar";

//Getting current XML from the Greek Archdiocese website
$liturgicday = "https://onlinechapel.goarch.org/daily.asp";
$xml = file_get_contents($liturgicday, "r") or die("Failed to load");
$oc_xml = simplexml_load_string($xml);
$fasting = $oc_xml->fasting;
	switch ($fasting) {
		case "":
			$fasting = "No Fast";
			break;
	}

$formatteddate = $oc_xml->formatteddate;
$icon = $oc_xml->icon;
$lectionarytitle = $oc_xml->lectionarytitle;

//starting HTML==============================================
echo '<table>';
echo '<tr><td colspan=2><h5>'.$formatteddate.'</h5></td></tr>';
echo '<tr><td colspan=2><h6>'.$lectionarytitle.'</h6><strong>'.$fasting.'</strong></td></tr>';
echo '<tr><td width="70%">';
echo '<strong>Today we commemorate:</strong><ul>';


//extracting daily saintsfeasts=======================================
$cursaintfeast = '';
$cursaintfeastpublicurl = '';
foreach($oc_xml->saintsfeasts->saintfeast as $sf) {
	$cursaintfeast = $sf->title;
	$cursaintfeastpublicurl = $sf->publicurl;
	if ($cursaintfeastpublicurl == ''){
		echo '<li>'.$cursaintfeast.'</li>';}
	else {
		echo '<li><a href="'.$cursaintfeastpublicurl.'"  target="_blank">'.$cursaintfeast.'</a></li>';
	}
}
echo '</ul>';
echo '</td>';
echo '<td width="30%">';
echo '<figure class="wp-block-image alignleft"><img src="'.$icon.'"><figcaption class="wp-element-caption"></figcaption></figure>';
echo '</td></tr></table>';

//extracting daily readings=======================================
echo '<table><tr><td colspan=2><h6>Daily Readings</h6></td></tr>';
foreach($oc_xml->readings->reading as $rds) {
    $cururl = $rds->url;
    // getting url data
    $curxml = file_get_contents($cururl, "r") or die("Failed to load");
    $oc_curxml = simplexml_load_string($curxml);
	$curdisplaytitle = $oc_curxml->displaytitle;
	$curbody_en = '';
	$curbody_ar = '';
	foreach($oc_curxml->translation as $curtransl) {		
		$curlanguage = $curtransl->language;
		$curshorttitle = $curtransl->title;
		if ($curlanguage == 'en'){
			echo '<tr><td width=60%><strong>'.$curdisplaytitle;			
			echo '<br>'.$curshorttitle.'</strong></td>';
			$curbody_en = $curtransl->body;
		}	
		else {
			if ($curlanguage == 'ar'){
				echo '<td width=40% align="right"><div style="direction:rtl;" lang="ar"><br><strong>'.$curshorttitle.'</strong></div></td></tr>';
				$curbody_ar = $curtransl->body;
			}
		}
	}
	echo '<tr><td width=60%>'.$curbody_en.'</td>';
	echo '<td width=40% align="right"><div style="direction:rtl; font-size:110%;" lang="ar">'.$curbody_ar.'</div></td></tr>';
}
echo '<tr><td colspan=2><p class="has-small-font-size">© Greek Orthodox Archdiocese of America <a href="https://www.goarch.org/chapel" target="_blank" rel="noopener noreferrer">www.goarch.org/chapel</a></p></td></tr>';
echo '</table>';
?>
