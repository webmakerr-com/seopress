<?php

namespace SEOPressPro\Core;

defined('ABSPATH') or exit('Cheatin&#8217; uh?');

class Kernel extends \SEOPress\Core\Kernel {

    protected static function getNamespacePrefix() {
        return '\\SEOPressPro\\';
    }

    protected static function getPluginsLoadedPriority() {
        return 20;
    }
}
