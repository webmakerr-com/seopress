<?php

namespace SEOPressPro\Actions\Api\PageSpeed;

if ( ! defined('ABSPATH')) {
    exit;
}

class GetPageSpeedReport extends \SEOPress\Actions\Api\PageSpeed\GetPageSpeedReport {

    protected function shouldRegister() {
        return true;
    }
}
