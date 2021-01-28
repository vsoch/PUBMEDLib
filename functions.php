<?php

// helper function to print results
function printTag($tags){
  foreach($tags as $t) {
    echo $t['token'] . "/" . $t['tag'] . " ";
}
  echo "\n";
}

function getWordType($tags) {
  $idx = getTag($tags);
  $words = array();
  $i=1;
  foreach($idx as $id) {
    $words[$i] = array('idx' => $id, 'token' => $tags[$id]['token'], 'tag' => $tags[$id]['tag']);
    $i++; 
  } 
  return $words;
}

function getTag($tags) {
  // We will save indices of words that we used
  $wordidx = [];
  // Select random sample of 10 words
  for ($x=0; $x<=10; $x++) {
    array_push($wordidx,rand(0,count($tags)));
  }
  return $wordidx;
  
}


// checkValid makes sure we have enough valid words to display
function checkValid($words) {

	$g=0;
	$goodtags = array('VBZ','VBP','VBN','VBG','VBD','VB','UH','RBR','RB','PRP$','PRP','NP','NNS','NN','JJ','IN');
        // Here we will save an array of idx
        $idxs = "";
	foreach ($words as $w) {
          $tag = $w['tag'];
  	  // If the tag is one we care about
          if (in_array($tag,$goodtags)) {
            $g++; 
          }
	}
          if ($g == 0){
          return 1; //keep going
         } else {
          return 0; //stop
         }
}

// returns form for tags
function displayForm($words,$pid) {
	echo "<div id=\"termbox\">\n";
        echo "<fieldset>\n";
        echo "<br><form action=\"view.php\" method=\"get\">\n\t<input type=\"hidden\" name=\"id\" value=\"$pid\" />\n\t\n";	

        $goodtags = array('VBZ','VBP','VBN','VBG','VBD','VB','UH','RBR','RB','PRP$','PRP','NP','NNS','NN','JJ','IN');

	$g=0;
        // Here we will save an array of idx
        $idxs = "";
	foreach ($words as $w) {
        	$tag = $w['tag'];
        	$token = $w['token'];
                $idx = $w['idx'];
                        
                // If the tag is one we care about
                if (in_array($tag,$goodtags)) {
                    $idxs = $idxs . "|" .$idx;
                    $g++; 
                    // Convert abbreviation to word type
                    switch($tag) {
                	case 'NN': $wordtype = "NOUN"; break;
		        case 'IN': $wordtype = "PREPOSITION"; break;
		        case 'VBZ': $wordtype = "VERB, 3RD, SINGULAR PRESENT"; break;
		        case 'VBP': $wordtype = "VERB, NON 3RD PERSON, SINGULAR PRESENT"; break;
		        case 'VBN': $wordtype = "VERB, PAST PARTICIPLE"; break;
			case 'VBG': $wordtype = 'VERB, PRESENT PARTICIPLE'; break;
		        case 'VBD': $wordtype = 'VERB, PAST TENSE'; break;
			case 'VB': $wordtype = 'VERB, BASE FORM'; break;
			case 'UH': $wordtype = 'EXCLAMATION'; break;
			case 'RBR': $wordtype = 'COMPARATIVE ADVERB'; break;
			case 'RB': $wordtype = 'ADVERB'; break;
			case 'PRP$': $wordtype = 'POSSESSIVE PRONOUN'; break;
			case 'PRP': $wordtype = 'PERSONAL PRONOUN'; break;
			case 'NP': $wordtype = 'PROPER NOUN'; break;
			case 'NNS': $wordtype = 'PLURAL NOUN'; break;
			case 'JJ': $wordtype = 'ADJECTIVE'; break;
			default: $wordtype = $tag;
	  	   }
                 echo "\t\t<p><input id=\"$idx\" type=\"text\" name=\"$g\" required placeholder=\"$wordtype\" /></p>\n";	
                 
                 }
        } 
        //Send hidden index variable
        echo "<input type=\"hidden\" name=\"idxs\" value=\"$idxs\" />";
	echo "\t\t<input id=\"submit\" type=\"submit\" value=\"Submit\" />\n\t\n";
        echo "</fieldset>\n</form>\n";
	echo "</div>\n";
}

// mad lib class
class madlibs {
	
	// default constructor for a lib
	function madlibs($words,$idxs,$abstract) {
		$this->words = $words;
                $this->idxs = $idxs;
                $this->abstract = $abstract;
                $this->tags = $this->getTags($abstract);	
	}
	
	// returns an arry of all tags in lib
	function getTags($abstract) {
          include('PosTagger.php');
          // Figure out parts of speech
          $tagger = new PosTagger('lexicon.txt');
          $tags = $tagger->tag($abstract);
          return $tags;
	}
	
	
	// puts in input words in blanks
	function displayStory() {
                //$intidx = array_map('intval', explode(',', $this->idxs));
                $text = '';
                $wordcount = 0;
                for ($x=0; $x<=count($this->tags); $x++){
                  // If the word was changed, keep changed word, format red 
                  if (in_array($x,$this->idxs)) {
                    $text = $text . " <span style=\"color:red\">" . " " . $this->words[$wordcount] . " </span>";
                    $wordcount++;
                   } else {
                    $text = $text . $this->tags[$x]['token'] . " ";
                   }
                  }      
            
                return $text;
	}

}
?>
