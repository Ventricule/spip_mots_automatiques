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
