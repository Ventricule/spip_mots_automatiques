<?php
/**
 * Utilisations de pipelines par Mots Automatiques
 *
 * @plugin     Mots Automatiques
 * @copyright  2020
 * @author     Pierre Tandille
 * @licence    GNU/GPL
 * @package    SPIP\Mots_automatiques\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function mots_automatiques_pre_edition($flux) {

    if (is_array($flux) and isset($flux['args']['type']) && $flux['args']['type'] == 'article') :

			include_spip('inc/filtres');
			include_spip('inc/config');
			include_spip('action/editer_liens');

			// ID de l'article en cours d'édition
			$id_article = $flux['args']['id_objet'];

			// ID du groupe mots automatiques
			$id_groupe = lire_config('mots_automatiques/id_groupe');

			// Nombre de mot-clés voulu
			$nombre_mots_automatiques = lire_config('mots_automatiques/nombre_mots_automatiques');

			// Extracteur choisi
			$extracteur = lire_config('mots_automatiques/extracteur');

			// Sélectionner tous les mots automatiques attribués à l'article
			$result = sql_select('liens.id_mot', 'spip_mots_liens AS liens JOIN spip_mots AS mots ON liens.id_mot=mots.id_mot', array("liens.id_objet=$id_article", "mots.id_groupe=$id_groupe"));
			$mots_automatiques = sql_fetch($result);

			// Si le nombre de mots automatiques est différent du nombre voulu, refaire l'extration
			if(sizeof($mots_automatiques) != $nombre_mots_automatiques) :

				// Supprime le lien avec les anciens mots automatiques
				$mots_groupe = sql_allfetsel('id_mot', 'spip_mots', "id_groupe=$id_groupe");
				$mots_groupe = array_map('reset', $mots_groupe);
				objet_dissocier(array('mot' => $mots_groupe), array('article' => $id_article));

				// Extraire les mots-clés
				$texte = supprimer_tags(propre($flux['data']['texte']));
				$mots = mots_automatiques_extraire($texte, $nombre_mots_automatiques, $extracteur);

				// Ajouter et/ou associer les mots
		    foreach ($mots as $mot) :

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

			endif;

    endif;

    return $flux;
}
