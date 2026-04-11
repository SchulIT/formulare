<?php

namespace App\Menu;

use App\Registry\FormRegistry;
use App\Security\Voter\FormVoter;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

readonly class Builder {


    public function __construct(
        private FactoryInterface $factory,
        private AuthorizationCheckerInterface $authorizationChecker,
        private FormRegistry $registry
    ) { }

    public function mainMenu(array $options): ItemInterface {
        $menu = $this->factory->createItem('root')
            ->setChildrenAttribute('class', 'navbar-nav me-auto');

        $menu->addChild('dashboard.label', [
            'route' => 'dashboard'
        ])
            ->setExtra('icon', 'fa fa-home');

        foreach($this->registry->getForms() as $form) {
            if($this->authorizationChecker->isGranted(FormVoter::Manage, $form)) {
                $menu->addChild($form->getName(), [
                    'route' => 'admin_show_form',
                    'routeParameters' => [
                        'alias' => $form->getAlias()
                    ]
                ]);
            }
        }

        return $menu;
    }
}