<?php
/* 
Plugin Name: UKM Stimuleringsmidler
Plugin URI: http://www.ukm-norge.no
Description: Info og inspirasjon for stimuleringsmidler
Author: UKM Norge / M Mandal 
Version: 1.0 
Author URI: http://mariusmandal.no
*/

use UKMNorge\Wordpress\Modul;

require_once('UKM/Autoloader.php');

class UKMstimuleringsmidler extends Modul {
    public static $action = 'forside';
    public static $path_plugin = null;

    /**
     * Register hooks
     */
    public static function hook() {
        add_action('user_admin_menu', ['UKMstimuleringsmidler','meny']);
		add_action('network_admin_menu', ['UKMstimuleringsmidler','network_meny']);
		
		// Vis dato for stimuleringsmidler
		if (get_option('pl_id')) {
			add_filter(
				'UKMWPDASH_messages',
				['UKMstimuleringsmidler','meldinger']
			);
		}
	}

	/**
	 * Legg til meny-elementer
	**/
	public static function meny() {
		$page = add_menu_page(
			'Stimuleringsmidler', 
			'Stimuleringsmidler', 
			'editor', 
			'UKMstimulering', 
			['UKMstimuleringsmidler','renderAdmin'],
			'dashicons-awards',
		    25
		);
		$subpage1 = add_submenu_page(
			'UKMstimulering', 
			'Søknadsskjema', 
			'Søknadsskjema', 
			'read', 
			'UKMstimulering_sok', 
			['UKMstimuleringsmidler','renderSoknadsskjema']
		);
		$subpage2 = add_submenu_page(
			'UKMstimulering', 
			'Rapport', 
			'Rapportskjema', 
			'read', 
			'UKMstimulering_rapport', 
			['UKMstimuleringsmidler','renderRapportskjema']
		);
		$subpage3 = add_submenu_page(
			'UKMstimulering', 
			'Inspirasjon', 
			'Inspirasjon', 
			'editor', 
			'UKMstimulering_idebank', 
			['UKMstimuleringsmidler','renderIdebank']
		);
		add_action(
			'admin_print_styles-' . $page,
			['UKMstimuleringsmidler','scripts_and_styles']
		);
		add_action(
			'admin_print_styles-' . $subpage1,
			['UKMstimuleringsmidler','scripts_and_styles']
		);
		add_action(
			'admin_print_styles-' . $subpage2,
			['UKMstimuleringsmidler','scripts_and_styles']
		);
		add_action(
			'admin_print_styles-' . $subpage3,
			['UKMstimuleringsmidler','scripts_and_styles']
		);
	}

	/**
	 * Legg til elementer i nettverksmeny
	 *
	 * @return void
	 */
	public static function network_meny() {
		$page = add_menu_page(
			'Stimuleringsmidler', 
			'Stimuleringsmidler', 
			'superadmin', 
			'UKMsmadmin', 
			['UKMstimuleringsmidler','renderNetworkAdmin'],
			'dashicons-awards' #'//ico.ukm.no/cash-menu.png'
		);
		add_action(
			'admin_print_styles-' . $page, 
			['UKMstimuleringsmidler','scripts_and_styles']
		);
	
	}

	/**
	 * Hook inn scripts og styles
	 *
	 * @return void
	 */
	public static function scripts_and_styles() {
		wp_enqueue_style('UKMwp_innhold_style');
		wp_enqueue_script('WPbootstrap3_js');
		wp_enqueue_style('WPbootstrap3_css');
	}

	/**
	 * Vis meldinger om søknadsfrister hvis aktuelt
	 *
	 * @param Array $MESSAGES
	 * @return Array $MESSAGES
	 */
	public static function meldinger( $MESSAGES ) {
		require_once('controller/network.controller.php');
		$frister = UKMstimulering_frister();
		
		$today = date('U');
		$redDate = $today+7*3600*24; // 7 days from today
		$yellowDate = $today+21*3600*24; // 3 weeks from today
		foreach ($frister as $frist) {
			if ($today < $frist->getTimestamp() && $redDate >= $frist->getTimestamp()) {
				$MESSAGES[] = array(
					'level' => 'alert-danger',
					'header' => 'Under én uke til frist for stimuleringsmidler!',
					'body' => 'Søknadsfrist: '.$frist->format("d.m").'.',
				);
			} 
			elseif ($today < $frist->getTimestamp() && $yellowDate >= $frist->getTimestamp()) {
				$MESSAGES[] = array(
					'level' => 'alert-warning',
					'header' => 'Snart frist for stimuleringsmidler',
					'body' => 'Søknadsfrist: '.$frist->format("d.m").'.'
				);
			}
		}
	
		return $MESSAGES;
	}

	/**
	 * Vis side med søknadsskjema
	 *
	 * @return void
	 */
	public static function renderSoknadsskjema() {
		static::setAction('soknadsskjema');
		static::renderAdmin();
	}
	
	/**
	 * Vis side med rapportskjema
	 *
	 * @return void
	 */
	public static function renderRapportskjema() {
		static::setAction('rapportskjema');
		static::renderAdmin();
	}
	
	/**
	 * Vis idébank-siden
	 *
	 * @return void
	 */
	public static function renderIdebank() {
		static::setAction('idebank');
		static::renderAdmin();
	}


	public static function renderNetworkAdmin() {
		$TWIGdata = array();
		require_once('controller/network.controller.php');
		$TWIGdata['frister'] = UKMstimulering_frister();
		$TWIGdata['f'] = UKMsmadmin_page();
		echo TWIG('network.html.twig', $TWIGdata, dirname(__FILE__));
	}
}

UKMstimuleringsmidler::init(__DIR__);
UKMstimuleringsmidler::hook();