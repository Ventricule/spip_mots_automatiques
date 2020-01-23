<?php

function formulaires_configurer_mots_automatiques_charger_dist(){
	$valeurs['nombre_mots_automatiques'] = lire_config('mots_automatiques/nombre_mots_automatiques');
	$valeurs['extracteur'] = lire_config('mots_automatiques/extracteur');
	$valeurs['id_groupe'] = lire_config('mots_automatiques/id_groupe');
	return $valeurs;
}

function formulaires_configurer_mots_automatiques_verifier_dist(){
	$erreurs = array();
	// verifie que les champs obligatoires sont bien la :
	foreach(array('nombre_mots_automatiques', 'extracteur') as $obligatoire)
		if (!_request($obligatoire)) $erreurs[$obligatoire] = 'Ce champ est obligatoire';

	// verifie que le nombre de mots-clés automatique est valide :
	$nombre_mots_automatiques = intval(_request('nombre_mots_automatiques'));
	if ($nombre_mots_automatiques < 0 || $nombre_mots_automatiques > 30)
		$erreurs['nombre_mots_automatiques'] = 'Veuillez choisir un chiffre entre 0 et 30';

	// verifie qu'un extracteur valide a été selectionné' :
	$extracteur = _request('extracteur');
	$extracteurs = array('api_yake');
	if (!in_array($extracteur, $extracteurs))
		$erreurs['extracteur'] = "L'extracteur choisi n'est pas valide.";

	if (count($erreurs))
		$erreurs['message_erreur'] = 'Votre saisie contient des erreurs !';
	return $erreurs;
}

function formulaires_configurer_mots_automatiques_traiter_dist(){
	ecrire_config('mots_automatiques/nombre_mots_automatiques', _request('nombre_mots_automatiques'));
	ecrire_config('mots_automatiques/extracteur', _request('extracteur'));
	return array('message_ok'=>'La nouvelle configuration a été enregistrée');
}
