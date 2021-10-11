<?php

namespace App\Registry;

use DateTime;

class FormRegistry {

    /** @var Form[] */
    private $forms;

    public function __construct() {
        $this->forms = [ ];
    }

    public function addForm(string $alias, array $options) {
        $form = new Form($alias, $options['name'], $options['form_class'], $options['items'], $options['role']);

        if($options['introduction']) {
            $form->setIntroductionParagraphs($options['introduction']);
        }

        if($options['success']) {
            $form->setSuccessParagraphs($options['success']);
        }

        $this->forms[$form->getAlias()] = $form;
    }

    public function hasForm(string $alias): bool {
        return isset($this->forms[$alias]);
    }

    /**
     * @param string $alias
     * @return Form
     * @throws FormNotFoundException
     */
    public function getForm(string $alias): Form {
        if(!isset($this->forms[$alias])) {
            throw new FormNotFoundException($alias);
        }

        return $this->forms[$alias];
    }

    /**
     * Returns all forms which a user has access to based on the given roles.
     *
     * @param string[] $roles
     * @return Form[]
     */
    public function getFormsForRoles(array $roles): array {
        $result = [ ];

        foreach($this->forms as $form) {
            if(in_array($form->getAdminRole(), $roles)) {
                $result[] = $form;
            }
        }

        usort($result, function(Form $formA, Form $formB) {
            return strnatcmp($formA->getName(), $formB->getName());
        });

        return $result;
    }

    public function getForms(): array {
        $result = $this->forms;

        usort($result, function(Form $formA, Form $formB) {
            return strnatcmp($formA->getName(), $formB->getName());
        });

        return $result;
    }
}