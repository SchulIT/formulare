<?php

namespace App\Settings;

use App\Registry\Form;
use DateTime;

class FormSettings extends AbstractSettings {

    private function getPrefixedKey(Form $form, string $key): string {
        return sprintf('form.%s.%s', $form->getAlias(), $key);
    }

    public function getFormStartDate(Form $form): ?DateTime {
        return $this->getValue($this->getPrefixedKey($form, 'start'), null);
    }

    public function setFormStartDate(Form $form, ?DateTime $start): void {
        $this->setValue($this->getPrefixedKey($form, 'start'), $start);
    }

    public function getFormEndDate(Form $form): ?DateTime {
        return $this->getValue($this->getPrefixedKey($form, 'end'), null);
    }

    public function setFormEndDate(Form $form, ?DateTime $start): void {
        $this->setValue($this->getPrefixedKey($form, 'end'), $start);
    }

    public function setFormMaxSubmissions(Form $form, ?int $maxSubmissions): void {
        $this->setValue($this->getPrefixedKey($form, 'max_submissions'), $maxSubmissions);
    }

    public function getFormMaxSubmissions(Form $form): ?int {
        return $this->getValue($this->getPrefixedKey($form, 'max_submissions'));
    }
}