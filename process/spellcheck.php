<?php

header('Content-type: application/json');
header('Content-Disposition: inline; filename="spellcheck.json"');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', FALSE); 
header('Pragma: no-cache');

function spellcheck($word, $dict_link) {

	return (pspell_check($dict_link, $word) || pspell_check($dict_link, strtolower($word)));
}

$dict = pspell_new_personal($_SERVER['DOCUMENT_ROOT']."/process/custom-dictionary.pws","en", '', '', 'utf-8');

if (isset($_POST['content'])) {

	$content = $_POST['content'];
	$results = array();
	
	foreach ($content as $e) {
	
		$e_target = $e['target'];
		$e_content = $e['content'];
		
		$results[$e_target] = array();
		
		$e_words = preg_split("/[\s,\.\,]/", $e_content);
		
		foreach ($e_words as $word) {
		
			if (strlen($word) > 0) {
				
							
				//If misspelled and not a digit...
				if (!spellcheck($word, $dict) && !ctype_digit($word) && ctype_alnum($word)) {
					$results[$e_target][] = $word;
				}
			
			}
		
		}
	
	}
	
	echo json_encode($results);

}