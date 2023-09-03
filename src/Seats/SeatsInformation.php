<?php

namespace App\Seats;

class SeatsInformation {

    private array $available = [ ];

    private array $total = [ ];

    public function __construct(private readonly string $property, private readonly array $choices) { }

    public function getProperty(): string {
        return $this->property;
    }

    public function getChoices(): array {
        return $this->choices;
    }

    public function setAvailable(string $choice, int $available): void {
        $this->available[$choice] = $available;
    }

    public function setTotal(string $choice, int $seats): void {
        $this->total[$choice] = $seats;

        if(!array_key_exists($choice, $this->available)) {
            $this->setAvailable($choice, $seats);
        }
    }

    public function getTotal(string $choice): int {
        return $this->total[$choice] ?? 0;
    }

    public function getAvailable(string $choice): int {
        return $this->available[$choice] ?? 0;
    }

    public function decreaseAvailable(string $choice): void {
        if($this->available[$choice] > 0) {
            $this->available[$choice]--;
        }
    }

    public function hasAvailableSeats(): bool {
        foreach($this->available as $choice => $available) {
            if($available > 0) {
                return true;
            }
        }

        return false;
    }

}