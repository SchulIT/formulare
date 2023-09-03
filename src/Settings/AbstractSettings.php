<?php

namespace App\Settings;

abstract class AbstractSettings {

    public function __construct(private readonly SettingsManager $settingsManager) {
    }

    protected function getValue($key, $default = null) {
        return $this->settingsManager
            ->getValue($key, $default);
    }

    protected function setValue($key, $value): void {
        $this->settingsManager
            ->setValue($key, $value);
    }
}