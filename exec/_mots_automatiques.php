<?php


if (!defined("_ECRIRE_INC_VERSION")) return;



function exec__mots_automatiques_dist(){
  include_spip('mots_automatiques_fonctions');

  $id_article = $_POST["id_article"];

	$mots = mots_automatiques($id_article);

	if ($mots === true) :
		echo 'fait';
  elseif($mots === false):
    echo 'erreur';
  else:
    echo implode(", ", $mots);
	endif;
}
