<?
include "backoffice.inc.php";

$appl = new backoffice();

unset($appl->utente);
$appl->datiSessione = array();
$appl->ridireziona($appl->paginaLogin);

?>