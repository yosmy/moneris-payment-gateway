<?php

namespace Yosmy\Payment\Gateway\Moneris\Test;

use Symsonte\Cli\Server\Input\Resolution\Resource\Loader;
use Symsonte\Service\ConstructorDeclaration;
use Symsonte\Service\Declaration\Call;
use Symsonte\Service\Declaration\ObjectArgument;
use Symsonte\ServiceKit\Declaration;
use Symsonte\ServiceKit\Declaration\Bag\Updater;

/**
 * @ds\service({tags: ['symsonte.service_kit.declaration.bag.updater']})
 */
class CliResolutionUpdater implements Updater
{
    /**
     * @var Loader
     */
    private $loader;

    /**
     * @param Loader $loader
     *
     * @ds\arguments({
     *     loader: "@symsonte.cli.server.input.resolution.resource.loader"
     * })
     */
    public function __construct(
        Loader $loader
    ) {
        $this->loader = $loader;
    }

    /**
     * @param Declaration $declaration
     *
     * @return Declaration
     */
    public function update(Declaration $declaration)
    {
        if (!$declaration->is('symsonte.cli.server.input.resolution.finder')) {
            return $declaration;
        }

        /** @var ConstructorDeclaration $internalDeclaration */
        $internalDeclaration = $declaration->getDeclaration();

        $internalDeclaration->addCall(
            new Call(
                'merge',
                [
                    new ObjectArgument($this->loader->load([
                        'dir' => sprintf('%s/../src', __DIR__),
                        'filter' => '*.php',
                        'extra' => [
                            'type' => 'annotation',
                            'annotation' => '/^cli\\\\resolution/'
                        ]
                    ])),
                ]
            )
        );

        return new Declaration(
            $internalDeclaration,
            $declaration->isDeductible(),
            $declaration->isPrivate(),
            $declaration->isDisposable(),
            $declaration->isLazy(),
            $declaration->getTags(),
            $declaration->getAliases(),
            $declaration->getCircularCalls()
        );
    }
}
