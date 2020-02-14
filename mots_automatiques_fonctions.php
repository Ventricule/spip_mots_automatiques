<?php

/**
 * Déclarations de balises pour les squelettes
 *
 * @package SPIP\Cextras\Fonctions
**/

// sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Retourne les mots clefs extrais avec l'API Yake
 *
 *
 * @param String $texte
 *     Le texte à analyser
 * @param String $nombre
 *     Le nombre de mots à retourner
 * @param String $extracteur
 *     Le nom de l'extracteur
 * @return Array
 *     Les mots-clés sous forme de tableau
**/
function mots_automatiques_extraire($texte, $nombre, $extracteur) {

  switch ($extracteur) :

    case "api_yake" :

      // Appel de l'API YAKE
      $curl = curl_init();
      $params = array(
        'content' => $texte
      );
      $params_string = http_build_query($params);
      $opts = [
          CURLOPT_URL => "http://yake.inesctec.pt/yake/v2/extract_keywords?max_ngram_size=2&number_of_keywords=$nombre",
          CURLOPT_POST => true,
          CURLOPT_POSTFIELDS => $params_string,
          CURLOPT_RETURNTRANSFER => true,
      ];
      curl_setopt_array($curl, $opts);
      $curl_response = curl_exec($curl);
      $response = json_decode($curl_response, true);
      $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
      curl_close($curl);

      if($http_status == 200 && is_array($response)) :

        // Créer un tableaux de mot-clés automatiques
        $mots = array();
        $keywords = $response['keywords'];
        foreach($keywords as $keyword):
          $mots[] = $keyword['ngram'];
        endforeach;

        return $mots;

      endif;
      break;

  endswitch;

}

/**
 * Cherche automatiquement les mots clés d'un article donné
 *
 * @param int $id_article
 * @return array mots clés automatiques
**/
function mots_automatiques($id_article) {
	include_spip('inc/texte');
	include_spip('inc/filtres');
	include_spip('inc/config');
	include_spip('action/editer_liens');

		// ID du groupe mots utilisé par Mots Automatiques
	$id_groupe = lire_config('mots_automatiques/id_groupe');

	// Nombre de mot-clés voulu
	$nombre_mots_automatiques = lire_config('mots_automatiques/nombre_mots_automatiques');

	// Extracteur choisi
	$extracteur = lire_config('mots_automatiques/extracteur');

	// Sélectionner tous les mots automatiques attribués à l'article
	$mots_automatiques = sql_allfetsel('liens.id_mot', 'spip_mots_liens AS liens INNER JOIN spip_mots AS mots ON liens.id_mot = mots.id_mot', array("liens.id_objet=$id_article", "mots.id_groupe=$id_groupe"));

	// Si le nombre de mots automatiques est différent du nombre voulu, refaire l'extration
	if(sizeof($mots_automatiques) != $nombre_mots_automatiques) :

		// Supprime le lien avec les anciens mots automatiques
		$mots_groupe = sql_allfetsel('id_mot', 'spip_mots', "id_groupe=$id_groupe");
		$mots_groupe = array_map('reset', $mots_groupe);
		objet_dissocier(array('mot' => $mots_groupe), array('article' => $id_article));

		// Extraire les mots-clés
		$texte = sql_getfetsel('texte', 'spip_articles', 'id_article=' . $id_article);
		$texte = supprimer_tags(propre($texte));
    if(strlen($texte) < 100) { return true; }
		$mots_automatiques = mots_automatiques_extraire($texte, $nombre_mots_automatiques, $extracteur);


		// Ajouter et/ou associer les mots
    if(is_array($mots_automatiques)):

  		foreach ($mots_automatiques as $mot) :

  			// Cherche si le mot existe déjà
  			$row = sql_fetsel('id_mot', 'spip_mots', array("id_groupe=$id_groupe", "titre='$mot'"));
  			if($row) :

  				// Associer le mot
  				objet_associer(array('mot' => $row['id_mot']), array('article' => $id_article));

  			else:

  				// Trouver le nom du groupe (type)
  				$row = sql_fetsel('titre', 'spip_groupes_mots', "id_groupe=$id_groupe");

  				if ($row) :

  					// Ajouter le mot
  					$champs = array(
  						'id_groupe' => $id_groupe,
  						'type' => $row['titre'],
  						'titre' => $mot
  					);
  					$id_mot = sql_insertq('spip_mots', $champs);

  					// Associer le mot
  					objet_associer(['mot' => $id_mot], ['article' => $id_article]);

  				endif;

  			endif;

  		endforeach;

      return $mots_automatiques;

  	endif;

    //retourne false si la reqète a échoué
    return false;

  endif;

  return true;
}
