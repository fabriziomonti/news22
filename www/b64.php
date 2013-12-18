<?php
//******************************************************************************
include "news22.inc.php";

// istanzia la pagina
$appl = new news22();

header("Content-Type: text/html; charset=utf-8");			

if ($_POST)
	{
	$convertita = base64_encode($_POST['da_convertire']);
	echo "la stringa <b>$_POST[da_convertire]</b> convertita in base64 è: <br><br>" .
	$convertita .
	"<p />";
	
	echo "quindi l'indirizzo da richiamare per usare la tua ui è:\n<p/>\n" .
			"<a href='http://$_SERVER[HTTP_HOST]$appl->httpwd/street?ui_url=$convertita'>" .
			"http://$_SERVER[HTTP_HOST]$appl->httpwd/street?ui_url=$convertita" .
			"</a><p/>";
	
	}

	
echo "<form method='post'>\n" .
			"<label for='da_convertire'>Stringa da convertire</label><br>\n" .
			"<input name='da_convertire' id='da_convertire' value='$_POST[da_convertire]'><br>\n" .
			"<input type='submit' value='converti'>\n" .
			"</form>\n";