<?php

namespace SEOPressPro\Actions\Api;

if (! defined('ABSPATH')) {
    exit;
}

class Redirections extends \SEOPress\Actions\Api\Redirections {
    protected function shouldRegister() {
        return true;
    }
}
