<?php

defined( 'ABSPATH' ) or die();

/**
 * Logs WordPress core updates
 */
class SimpleCoreUpdatesLogger extends SimpleLogger
{

	public $slug = __CLASS__;

	public function loaded() {
		
		add_action( '_core_updated_successfully', array( $this, "on_core_updated" ) );

		add_action( 'update_feedback', array( $this, "on_update_feedback" ) );

	}

	/**
	 * We need to store the WordPress version we are updating from. 
	 * 'update_feedback' is a suitable filter.
	 */
	function on_update_feedback() {

		if ( ! empty( $GLOBALS['wp_version'] ) && ! isset( $GLOBALS['simple_history_' .  $this->slug . '_wp_version'] ) ) {
			$GLOBALS['simple_history_' .  $this->slug . '_wp_version'] = $GLOBALS['wp_version'];
		}

	}

	/**
	 * Get array with information about this logger
	 * 
	 * @return array
	 */
	function getInfo() {

		$arr_info = array(			
			"name" => "Core Updates Logger",
			"description" => "Logs the update of WordPress (manual and automatic updates)",
			"capability" => "update_core",
			"messages" => array(
				'core_updated' => __('Updated WordPress to {new_version} from {prev_version}', 'simple-history'),
				'core_auto_updated' => __('WordPress auto-updated to {new_version} from {prev_version}', 'simple-history')
			),
			"labels" => array(
				"search" => array(
					"label" => _x("WordPress Core", "User logger: search", "simple-history"),
					"options" => array(
						_x("WordPress core updates", "User logger: search", "simple-history") => array(
							"core_updated",
							"core_auto_updated"
						),						
					)
				) // end search array
			) // end labels
		);
		
		return $arr_info;

	}

	/**
	 * Called when WordPress is updated
	 *
	 * @param string $new_wp_version
	 */
	public function on_core_updated($new_wp_version) {
		
		$old_wp_version = empty( $GLOBALS['simple_history_' .  $this->slug . '_wp_version'] ) ? $GLOBALS["wp_version"] : $GLOBALS['simple_history_' .  $this->slug . '_wp_version'];

		$auto_update = true;		
		if ( $GLOBALS['pagenow'] == 'update-core.php' ) {
			$auto_update = false;
		}

		if ($auto_update) {
			$message = "core_auto_updated";
		} else {
			$message = "core_updated";
		}

		$this->noticeMessage(
			$message,
			array(
				"prev_version" => $old_wp_version,
				"new_version" => $new_wp_version
			)
		);

	}

}
