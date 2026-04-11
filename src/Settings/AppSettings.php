<?php

namespace App\Settings;

class AppSettings extends AbstractSettings {
    public function getCustomCss(): ?string {
        return $this->getValue('app.custom_css', null);
    }

    public function setCustomCss(?string $css): void {
        $this->setValue('app.custom_css', $css);
    }
}