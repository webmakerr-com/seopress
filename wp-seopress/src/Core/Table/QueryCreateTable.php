<?php // phpcs:ignore

namespace SEOPress\Core\Table;

defined( 'ABSPATH' ) || exit;

use SEOPress\Models\Table\TableInterface;
use SEOPress\Models\Table\TableColumnInterface;

/**
 * QueryCreateTable
 */
class QueryCreateTable {

        /**
         * QueryExistTable helper.
         *
         * @var QueryExistTable
         */
        protected $query_exist_table;

        public function __construct( QueryExistTable $query_exist_table = null ) {
                $this->query_exist_table = $query_exist_table ?: new QueryExistTable();
        }


        public function constructColumn( TableColumnInterface $column, $is_alter = false ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
                $line = sprintf( '%s %s', $column->getName(), $column->getType() );
                if ( $column->getPrimaryKey() && ! $is_alter ) {
                        $line .= ' NOT NULL AUTO_INCREMENT';
                } else {
                        $default_value = $column->getDefaultValue();
                        if ( null !== $default_value ) {
                                $line .= sprintf( " DEFAULT '%s'", $default_value );
                        } else {
                                $line .= ' DEFAULT NULL';
                        }
                }

		return $line;
	}

        public function getPrimaryKey( $columns ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
                $value = '';
		foreach ( $columns as $key => $column ) {
			if ( ! $column->getPrimaryKey() ) {
				continue;
			}

			if ( empty( $value ) ) {
				$value .= 'PRIMARY KEY (';
			} else {
				$value .= ', ';
			}

			$value .= $column->getName();
		}
		if ( ! empty( $value ) ) {
			$value .= ')';
		}

                return $value;
        }

        /**
         * Add missing columns to an existing table.
         *
         * @param TableInterface $table The table definition.
         *
         * @return bool|void
         */
        public function addMissingColumns( TableInterface $table ) {
                global $wpdb;

                $new_columns = array();
                $columns     = $table->getColumns();
                $table_name  = $wpdb->prefix . $table->getName();

                foreach ( $columns as $column ) {
                        if ( ! $this->query_exist_table->columnExists( $table, $column ) ) {
                                $new_columns[] = $this->constructColumn( $column, true );
                        }
                }

                if ( empty( $new_columns ) ) {
                        return true;
                }

                $sql = "ALTER TABLE {$table_name} ADD (" . implode( ', ', $new_columns ) . ')';

                try {
                        $wpdb->query( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
                } catch ( \Exception $e ) {
                        return false;
                }

                return true;
        }

	/**
	 * The create function.
	 *
	 * @param TableInterface $table The table.
	 *
	 * @return bool
	 */
	public function create( TableInterface $table ) {

		global $wpdb;

		$charset = $wpdb->get_charset_collate();

		$indexes = array();

		$data    = array();
		$columns = $table->getColumns();
		foreach ( $columns as $key => $column ) {
			$data[ $key ] = $this->constructColumn( $column );

			$column_indexable = $column->getIndex();
			if ( ! $column_indexable ) {
				continue;
			}

			$indexes[] = "CREATE INDEX idx_{$column->getName()} ON {$wpdb->prefix}{$table->getName()} ({$column->getName()})";
		}

		$primary_key = $this->getPrimaryKey( $columns );

		if ( ! empty( $primary_key ) ) {
			$data[] = $primary_key;
		}

		$table_name = $wpdb->prefix . $table->getName();

		$sql   = array();
		$sql[] = "CREATE TABLE {$table_name} (";
		$sql[] = implode( ', ', $data );
		$sql[] = ") {$charset};";

		$sql = implode( "\n", $sql );

		if ( ! function_exists( 'dbDelta' ) ) {
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		}

		try {
			maybe_create_table( $table_name, $sql );
		} catch ( \Exception $e ) {
			return false;
		}

		try {
			foreach ( $indexes as $index ) {
				$wpdb->query( $index ); // phpcs:ignore -- TODO: prepare and use placeholder.
			}
		} catch ( \Exception $e ) {
			return false;
		}
	}
}
