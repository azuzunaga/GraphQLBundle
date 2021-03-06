<?php

declare(strict_types=1);

namespace Overblog\GraphQLBundle\Config;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

abstract class TypeWithOutputFieldsDefinition extends TypeDefinition
{
    /**
     * @param string $name
     *
     * @return ArrayNodeDefinition|\Symfony\Component\Config\Definition\Builder\NodeDefinition
     */
    protected function outputFieldsSelection(string $name = 'fields')
    {
        $builder = new TreeBuilder();
        $node = $builder->root($name);
        $node
            ->isRequired()
            ->requiresAtLeastOneElement();

        /* @var ArrayNodeDefinition $prototype */
        $prototype = $node->useAttributeAsKey('name', false)->prototype('array');

        $prototype
            ->children()
                ->append($this->typeSelection())
                ->arrayNode('args')
                    ->info('Array of possible type arguments. Each entry is expected to be an array with following keys: name (string), type')
                    ->useAttributeAsKey('name', false)
                    ->prototype('array')
                        // Allow arg type short syntax (Arg: Type => Arg: {type: Type})
                        ->beforeNormalization()
                            ->ifTrue(function ($options) {
                                return \is_string($options);
                            })
                            ->then(function ($options) {
                                return ['type' => $options];
                            })
                        ->end()
                        ->children()
                            ->append($this->typeSelection(true))
                            ->append($this->descriptionSection())
                            ->append($this->defaultValueSection())
                        ->end()
                    ->end()
                ->end()
                ->variableNode('resolve')
                    ->info('Value resolver (expression language can be use here)')
                ->end()
                ->append($this->descriptionSection())
                ->append($this->deprecationReasonSelection())
                ->variableNode('access')
                    ->info('Access control to field (expression language can be use here)')
                ->end()
                ->variableNode('public')
                    ->info('Visibility control to field (expression language can be use here)')
                ->end()
                ->variableNode('complexity')
                    ->info('Custom complexity calculator.')
                ->end()
            ->end();

        return $node;
    }
}
