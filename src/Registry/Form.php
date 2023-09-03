<?php

namespace App\Registry;

class Form {

    /** @var string[] */
    private array $introductionParagraphs = [ ];

    /** @var string[] */
    private array $successParagraphs = [ ];

    private array $choices = [ ];

    public function __construct(private readonly string $alias, private readonly string $name, private readonly string $formClass, private readonly array $items, private readonly string $adminRole) { }

    /**
     * @return string
     */
    public function getAlias(): string {
        return $this->alias;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getFormClass(): string {
        return $this->formClass;
    }

    /**
     * @return array
     */
    public function getItems(): array {
        return $this->items;
    }

    /**
     * @return string
     */
    public function getAdminRole(): string {
        return $this->adminRole;
    }

    /**
     * @return string[]
     */
    public function getIntroductionParagraphs(): array {
        return $this->introductionParagraphs;
    }

    /**
     * @param string[] $introductionParagraphs
     * @return Form
     */
    public function setIntroductionParagraphs(array $introductionParagraphs): Form {
        $this->introductionParagraphs = $introductionParagraphs;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getSuccessParagraphs(): array {
        return $this->successParagraphs;
    }

    /**
     * @param string[] $successParagraphs
     * @return Form
     */
    public function setSuccessParagraphs(array $successParagraphs): Form {
        $this->successParagraphs = $successParagraphs;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getChoices(): array {
        return $this->choices;
    }

    /**
     * @param string[] $choices
     * @return Form
     */
    public function setChoices(array $choices): Form {
        $this->choices = $choices;
        return $this;
    }

    public function getCountableProperty(): ?string {
        foreach($this->items as $key => $item) {
            if(isset($item['count']) && $item['count'] === true) {
                return $key;
            }
        }

        return null;
    }

    public function hasCountableProperty(): bool {
        return $this->getCountableProperty() !== null;
    }

    public function getSeats(): array {
        $result = [ ];

        foreach($this->items as $key => $item) {
            if(isset($item['seats'])) {
                $result[$key] = $item;
            }
        }

        return $result;
    }

    /**
     * @return bool
     */
    public function hasSeats(): bool {
        foreach($this->items as $item) {
            if(isset($item['seats'])) {
                return true;
            }
        }

        return false;
    }
}