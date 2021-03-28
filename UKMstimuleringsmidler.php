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
	}

	/**
	 * Legg til meny-elementer
	**/
	public static function meny() {
		$page = add_menu_page(
			'Stimuleringsmidler', 
			'Stimuleringsmidler', 
			'subscriber', 
			'UKMstimulering', 
			['UKMstimuleringsmidler','renderAdmin'],
			'dashicons-awards',
		    50
		);

		# Bytt til arrangor
		switch_to_blog( UKM_HOSTNAME == 'ukm.dev' ? 13 : 881 );
		
		# Hent alle sider
		$parent_page = get_page_by_path( 'stimuleringsmidler' );
		# Hent alle sider
		$my_wp_query = new WP_Query();
		$children_pages = $my_wp_query->query( array('post_parent' => $parent_page->ID, 'post_type'=>'page', 'posts_per_page' => 100, 'orderby' => 'menu_order', 'order' => 'ASC') );

		# Restore til aktiv side
		### OBS - MÅ GJØRES FØR LOOPEN FOR Å KUNNE LEGGE TIL SIDER (ingen av brukerne har editor på arrangørbloggen!)
		restore_current_blog();

		# Legg til menyelementer og enqueue scripts + styles
		foreach( $children_pages as $child ) {
			$subpage = add_submenu_page(
				'UKMstimulering', 
				$child->post_title,
				$child->post_title, 
				'subscriber', //Deffinerer hva slags brukerrettigheter brukeren måtte ha for å vise menyvalg "Verktøykasse"
				'UKMstimulering_'.$child->post_name, 
				[static::class, 'renderSubpage']
			);
			// add_action( 'admin_print_styles-' . $subpage, 'UKMide_scripts_and_styles' );	

			add_action('admin_print_styles-' . $subpage, ['UKMstimuleringsmidler','scripts_and_styles']);
		}
	}

	function renderSubpage() {
		static::setAction('pagecontainer');
		// echo TWIG($VIEW. '.twig.html', $TWIGdata, dirname(__FILE__));
		return static::renderAdmin();
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