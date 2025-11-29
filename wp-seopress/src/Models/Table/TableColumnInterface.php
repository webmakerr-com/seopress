<?php // phpcs:ignore

namespace SEOPress\Models\Table;

defined( 'ABSPATH' ) || exit;

/**
 * TableColumnInterface
 */
interface TableColumnInterface {


	/**
	 * The getType function.
	 *
	 * @return int
	 */
	public function getType(); // phpcs:ignore -- TODO: check if method is outside this class before renaming.

	/**
	 * The getName function.
	 *
	 * @return string
	 */
        public function getName(); // phpcs:ignore -- TODO: check if method is outside this class before renaming.

        /**
         * The getPrimaryKey function.
         *
         * @return bool
         */
        public function getPrimaryKey(); // phpcs:ignore -- TODO: check if method is outside this class before renaming.

        /**
         * Whether the column should be indexed.
         *
         * @return bool
         */
        public function getIndex();

        /**
         * Default value for the column.
         *
         * @return mixed
         */
        public function getDefaultValue();
}
