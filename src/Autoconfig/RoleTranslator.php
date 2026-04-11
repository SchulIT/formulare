<?php

namespace App\Autoconfig;

use App\Registry\FormRegistry;
use Override;
use SchulIT\CommonBundle\Autoconfig\Roles\RoleTranslatorInterface;
use SchulIT\CommonBundle\Autoconfig\Roles\TranslationFileTranslator;
use Symfony\Contracts\Translation\TranslatorInterface;

readonly class RoleTranslator implements RoleTranslatorInterface {

    public function __construct(
        private TranslatorInterface $translator,
        private TranslationFileTranslator $translationFileTranslator,
        private FormRegistry $formRegistry
    ) {

    }

    #[Override]
    public function translate(string $roleName): string|null {
        foreach($this->formRegistry->getForms() as $form) {
            if($form->getName() === $roleName) {
                return $this->translator->trans(
                    sprintf('roles.%s', $roleName),
                    parameters: [
                        '%form%' => $form->getName(),
                    ],
                    domain: 'autoconfig',
                );
            }
        }

        return $this->translationFileTranslator->translate($roleName);
    }
}