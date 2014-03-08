<?php
include "street.inc.php";

$appl = new street();

unset($appl->utente);
$appl->datiSessione = array();
$appl->ridireziona("index.php");