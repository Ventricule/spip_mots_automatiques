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

function mots_automatiques_post_edition($flux) {

    if (is_array($flux) and isset($flux['args']['table']) && $flux['args']['table'] == 'spip_articles') :

			spip_log($flux, 'mots_automatiques');

			// ID de l'article en cours d'édition
			$id_article = intval($flux['args']['id_objet']);

			mots_automatiques($id_article);

    endif;

    return $flux;
}

/**
 * Ajoute les scripts JS de Mots Automatiques dans l'espace privé
 *
 * @param string $flux
 * @return string
**/
function mots_automatiques_header_prive($flux) {

	$js = find_in_path('javascript/mots_automatiques.js');
	$flux .= "\n<script type='text/javascript' src='$js' id='no_cache'></script>\n";

	return $flux;
}
