<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin Mots Automatiques
 *
 * @plugin     Mots Automatiques
 * @copyright  2020
 * @author     Pierre Tandille
 * @licence    GNU/GPL
 * @package    SPIP\Mots_automatiques\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function mots_automatiques_create() {

}

/**
 * Fonction d'installation et de mise à jour du plugin Mots Automatiques.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
**/
function mots_automatiques_upgrade($nom_meta_base_version, $version_cible) {

	include_spip('inc/config');
	include_spip('action/editer_groupe_mots');

	// Vérifier la présence d'un groupe de mots-clés pour Mots Automatiques
	if( $sql = sql_fetsel("id_groupe", "spip_groupes_mots", "titre='Mots Automatiques'") ):
		$id_groupe = $sql['id_groupe'];

	// Sinon créer un groupe de mots-clés
	else:
		$id_groupe = lire_config('mots_automatiques/id_groupe') ;
		if( !$id_groupe || !sql_countsel("spip_groupe_mots", "id_groupe=$id_groupe")) :
			$set = array(
				'titre' => 'Mots Automatiques',
				'unseul' => 'non',
				'obligatoire' => 'non',
				'tables_liees' => 'articles',
				'minirezo' => 'non',
				'comite' => 'non',
				'forum' => 'non',
				'technique' => 'oui'
			);
			$id_groupe = groupe_mots_inserer('articles', $set);
		endif;
	endif;

	$maj = array();

	$maj['create'] = array(
    array('ecrire_config', 'mots_automatiques/id_groupe', $id_groupe),
    array('ecrire_config', 'mots_automatiques/nombre_mots_automatiques', 20),
    array('ecrire_config', 'mots_automatiques/extracteur', 'api_yake'),
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);

}


/**
 * Fonction de désinstallation du plugin Mots Automatiques.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
**/
function mots_automatiques_vider_tables($nom_meta_base_version) {

	/* Option : supprimer les mots clés au moment de désinstaller -> bloqué par le système d'autorisations
	include_spip('action/supprimer_groupe_mots');
	action_supprimer_groupe_mots_dist(lire_config('mots_automatiques/id_groupe'));
	*/

	include_spip('inc/config');
	effacer_config('mots_automatiques/nombre_mots_automatiques');
	effacer_config('mots_automatiques/id_groupe');

	effacer_meta($nom_meta_base_version);

}
