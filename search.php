<html>
<head>

<meta charset="utf-8">
        <title>PUBMEDLib</title>
        <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Varela+Round">
        <link rel="stylesheet" href="style.css">

        <!--[if lt IE 9]>
                <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->


</head>
<?php

// include POS parser
include 'functions.php';
include 'PosTagger.php';
		
// Collect search terms from browser
if (isset($_POST['name'])) {
	$term = $_POST['name'];
	$term = htmlspecialchars($term);

        // Only return 100 results
        $nres = 100;
        $maxdate = 360;        // no more than 360 days old
        $rettype = 'abstract'; // only retrieve abstract
        $retmode = 'xml';      // return xml result
 
	// Write query
        $query = "http://eutils.ncbi.nlm.nih.gov/entrez/eutils/esearch.fcgi?db=pubmed&term=$term&reldate=$maxdate&retmax=$nres&rettype=$rettype&retmode=$retmode&apikey=894ad8b29e6c61c4bbbb93a07c97b625&";
	
        $content = simplexml_load_file($query); 
        $pids = $content->IdList->Id;
        
	$keepgoing = 1;
        while ($keepgoing == 1) { // if we don't have enough words

		// Grab a random pid
		$num = rand(0,count($pids));
		$pid = $pids[$num];
	      
		// Get article abstract
		$query = "http://eutils.ncbi.nlm.nih.gov/entrez/eutils/efetch.fcgi?db=pubmed&id=$pid&rettype=xml";

		$content = simplexml_load_file($query);
		$title = $content->PubmedArticle->MedlineCitation->Article->ArticleTitle;
		$abstract = $content->PubmedArticle->MedlineCitation->Article->Abstract->AbstractText;        

		// Figure out parts of speech
		$tagger = new PosTagger('lexicon.txt');
		$tags = $tagger->tag($abstract);
		
		// Get word types to display
		$words = getWordType($tags); 

		// Make sure we have enough to display
		$keepgoing = checkValid($words);        

	}
        // Display form for madlib, and pass through $pid
        displayForm($words,$pid);  

}
else {
                echo "<div id=\"aresult\">\n";
                echo "<fieldset>\n";
                echo 'I can\'t search without <a href="http://www.vbmis.com/bmi/project/PUBMEDLib">a term!</a>';
                echo "</fieldset>\n</div>\n";
}

?>
</html>
