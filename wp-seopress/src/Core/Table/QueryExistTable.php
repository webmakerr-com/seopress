<?php // phpcs:ignore

namespace SEOPress\Core\Table;

defined( 'ABSPATH' ) || exit;

use SEOPress\Models\Table\TableInterface;
use SEOPress\Models\Table\TableColumnInterface;

/**
 * QueryExistTable
 */
class QueryExistTable {

	/**
	 * The exist function.
	 *
	 * @param TableInterface $table The table.
	 *
	 * @return bool
	 */
        public function exist( TableInterface $table ) {

		global $wpdb;

		$query = "SHOW TABLES LIKE '{$wpdb->prefix}{$table->getName()}'";
		try {
			$result = $wpdb->query( $query ); // phpcs:ignore -- TODO: prepare and use placeholder.

			if ( 0 === $result ) {
				return false;
			}

			return true;
		} catch ( \Exception $e ) {
			return false;
                }
        }

        /**
         * Check if a column exists in a given table.
         *
         * @param TableInterface       $table  The table definition.
         * @param TableColumnInterface $column The column definition.
         *
         * @return bool
         */
        public function columnExists( TableInterface $table, TableColumnInterface $column ) {
                global $wpdb;

                $table_name  = $wpdb->prefix . $table->getName();
                $column_name = $column->getName();

                $query = $wpdb->prepare( "SHOW COLUMNS FROM {$table_name} LIKE %s", $column_name );
                $result = $wpdb->get_var( $query );

                return ! empty( $result );
        }
}
