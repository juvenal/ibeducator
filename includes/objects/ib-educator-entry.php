<?php

class IB_Educator_Entry {
	public $ID = 0;
	public $course_id = 0;
	public $user_id = 0;
	public $payment_id = 0;
	public $grade = 0;
	public $entry_status = '';
	public $entry_date = '';
	public $complete_date = '';
	protected $table_name;

	/**
	 * Get instance.
	 *
	 * @param mixed $data
	 * @return IB_Educator_Entry
	 */
	public static function get_instance( $data = null ) {
		if ( is_numeric( $data ) ) {
			global $wpdb;
			$tables = ib_edu_table_names();
			$data = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . $tables['entries'] . " WHERE ID = %d", $data ) );
		}

		return new self( $data );
	}

	/**
	 * Get available statuses.
	 *
	 * @return array
	 */
	public static function get_statuses() {
		return array(
			'pending'    => __( 'Pending', 'ibeducator' ),
			'inprogress' => __( 'In progress', 'ibeducator' ),
			'complete'   => __( 'Complete', 'ibeducator' ),
			'cancelled'  => __( 'Cancelled', 'ibeducator' )
		);
	}

	/**
	 * @constructor
	 *
	 * @param array $data
	 */
	public function __construct( $data ) {
		global $wpdb;
		$tables = ib_edu_table_names();
		$this->table_name = $tables['entries'];

		if ( ! empty( $data ) ) {
			$this->ID = $data->ID;
			$this->course_id = $data->course_id;
			$this->user_id = $data->user_id;
			$this->payment_id = $data->payment_id;
			$this->grade = $data->grade;
			$this->entry_status = $data->entry_status;
			$this->entry_date = $data->entry_date;
		}
	}

	/**
	 * Save to database.
	 *
	 * @return boolean
	 */
	public function save() {
		global $wpdb;
		$affected_rows = 0;

		if ( is_numeric( $this->ID ) && $this->ID > 0 ) {
			$affected_rows = $wpdb->update(
				$this->table_name,
				array(
					'course_id'     => $this->course_id,
					'user_id'       => $this->user_id,
					'payment_id'    => $this->payment_id,
					'grade'         => $this->grade,
					'entry_status'  => array_key_exists( $this->entry_status, self::get_statuses() ) ? $this->entry_status : '',
					'entry_date'    => $this->entry_date,
					'complete_date' => $this->complete_date
				),
				array( 'ID' => $this->ID ),
				array( '%d', '%d', '%d', '%f', '%s', '%s', '%s' ),
				array( '%d' )
			);
		} else {
			$affected_rows = $wpdb->insert(
				$this->table_name,
				array(
					'course_id'     => $this->course_id,
					'user_id'       => $this->user_id,
					'payment_id'    => $this->payment_id,
					'grade'         => $this->grade,
					'entry_status'  => array_key_exists( $this->entry_status, self::get_statuses() ) ? $this->entry_status : '',
					'entry_date'    => $this->entry_date,
					'complete_date' => $this->complete_date
				),
				array( '%d', '%d', '%d', '%f', '%s', '%s', '%s' )
			);
			$this->ID = $wpdb->insert_id;
		}

		return ( 1 === $affected_rows || 0 === $affected_rows ) ? true : false;
	}

	/**
	 * Delete from database.
	 *
	 * @return boolean
	 */
	public function delete() {
		global $wpdb;
		
		if ( $wpdb->delete( $this->table_name, array( 'ID' => $this->ID ), array( '%d' ) ) ) {
			return true;
		}

		return false;
	}
}