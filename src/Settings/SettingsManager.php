<?php

namespace App\Settings;

use App\Entity\Setting;
use Doctrine\ORM\EntityManagerInterface;

class SettingsManager {

    private bool $initialized = false;
    private array $settings = [ ];

    public function __construct(private readonly EntityManagerInterface $em) {    }

    /**
     * Gets the value of a setting or $default if setting does not exist
     *
     * @param mixed|null $default Default value which is returned if the setting with key $key is non-existent
     * @return mixed|null
     */
    public function getValue(string $key, mixed $default = null): mixed {
        $this->initializeIfNecessary();

        if(isset($this->settings[$key])) {
            return $this->settings[$key]->getValue();
        }

        return $default;
    }

    /**
     * Sets the value of a setting
     */
    public function setValue(string $key, mixed $value): void {
        $this->initializeIfNecessary();

        if(!isset($this->settings[$key])) {
            $this->settings[$key] = (new Setting())
                ->setKey($key);
        }

        $setting = $this->settings[$key];
        $setting->setValue($value);

        $this->em->persist($setting);
        $this->em->flush();
    }

    /**
     * Checks whether to load all settings from the database and loads them if necessary
     */
    private function initializeIfNecessary(): void {
        if($this->initialized !== true) {
            $this->initialize();
        }
    }

    /**
     * Loads all settings from the database
     */
    protected function initialize(): void {
        $settings = $this->em->getRepository(Setting::class)
            ->findAll();

        foreach($settings as $setting) {
            $this->settings[$setting->getKey()] = $setting;
        }

        $this->initialized = true;
    }
}