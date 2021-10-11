<?php

namespace App\DependencyInjection;

use App\Registry\FormRegistry;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

class FormExtension extends Extension implements PrependExtensionInterface {

    /**
     * @inheritDoc
     */
    public function load(array $configs, ContainerBuilder $container) {
        $configuration = new FormConfiguration();
        $config = $this->processConfiguration($configuration, $configs);

        $definition = new Definition(FormRegistry::class);

        foreach($config as $alias => $options) {
            $definition->addMethodCall('addForm', [ $alias, $options ]);
        }

        $container->setDefinition(FormRegistry::class, $definition);
    }

    public function getAlias() {
        return 'form';
    }

    /**
     * @inheritDoc
     */
    public function prepend(ContainerBuilder $container) {
        $securityConfig = $container->getExtensionConfig('security')[0];
        $formsConfig = $container->getExtensionConfig('form');

        foreach($formsConfig as $formConfig) {
            foreach($formConfig as $alias => $formOptions) {
                $formSecurity = $formOptions['security'];

                if ($formSecurity !== null) {
                    $securityConfig = $this->prependConfig($securityConfig, $formSecurity);
                }
            }
        }

        // Ugly part: replace the config as prepending does not work :-(
        $reflectionClass = new \ReflectionClass($container);
        $attribute = $reflectionClass->getProperty('extensionConfigs');
        $attribute->setAccessible(true);

        $configs = $attribute->getValue($container);
        unset($configs['security']);
        $attribute->setValue($container, $configs);

        $container->loadFromExtension('security', $securityConfig);
    }

    private function prependConfig(array $original, array $prepend): array {
        $result = $prepend;

        // Check if all keys are numeric
        $allKeys = array_merge(array_keys($original), array_keys($prepend));
        $numericKeys = array_filter($allKeys, 'is_numeric');
        if(count($numericKeys) === count($allKeys)) {
            $result = array_merge($prepend, $original);
        } else {
            foreach ($original as $key => $value) {
                if (is_array($value)) {
                    $result[$key] = $this->prependConfig($value, $result[$key] ?? []);
                } else {
                    if (!isset($result[$key])) {
                        $result[$key] = $value;
                    }
                }
            }
        }

        return $result;
    }

}