<?php
	include 'functions.php';
	
	// if pid variables exist
	if($_GET) {
		$pid = htmlspecialchars($_GET['id']);
               
                // Get indices of words from browser
                $idxs = htmlspecialchars($_GET['idxs']);
                $idxs = explode("|",$idxs);
                array_shift($idxs);
                $words = array(); 

		// Get article abstract
		$query = "http://eutils.ncbi.nlm.nih.gov/entrez/eutils/efetch.fcgi?db=pubmed&id=$pid&rettype=xml";
 
                // Get user word choices
                for ($q = 1; $q <= 10; $q++){
                   if ($_GET[$q]){
                       array_push($words,$_GET[$q]);
                   }
                }
 
                // Create new Madlib with abstract, replacement words, and original indices:
                $content = simplexml_load_file($query);
	        $title = $content->PubmedArticle->MedlineCitation->Article->ArticleTitle;
        	$abstract = $content->PubmedArticle->MedlineCitation->Article->Abstract->AbstractText;        
                $lib = new madlibs($words,$idxs,$abstract);
	}	
?>
<html>
	<head>
		<title>PUBMEDLib Output</title>
	
                <meta charset="utf-8">
                <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Varela+Round">
                <link rel="stylesheet" href="style.css">
        <!--[if lt IE 9]>
                <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->

</head>

	<body>
		<?php 
		// if valid lib created
		if($lib) {
		  $text = $lib->displayStory();
                  echo "<div id=\"aresult\">\n";
                  echo "<fieldset>\n";
                  echo "<h2>" . $title . "</h2>\n<br>\n";
                  echo "<p>" . $text . "</p>\n";
                  echo "</fieldset>";
                  echo"</div>\n";
		} 
		// if user accesses url without proper get variables
		else {
                echo "<div id=\"aresult\">\n";
                echo "<fieldset>\n";  
	        echo 'Hey, what are you doing? Go back and <a href="http://www.vbmis.com/bmi/project/PUBMEDLib">make your own mad lib</a>';
                echo "</fieldset>\n</div>\n";
		}
		?>
	</body>
</html>
