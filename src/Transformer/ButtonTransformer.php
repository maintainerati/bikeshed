<?php

declare(strict_types=1);

namespace Maintainerati\Bikeshed\Transformer;

use Limenius\Liform\Transformer\AbstractTransformer;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeGuesserInterface;
use Symfony\Component\Translation\TranslatorInterface;

final class ButtonTransformer extends AbstractTransformer
{
    public function __construct(TranslatorInterface $translator, FormTypeGuesserInterface $validatorGuesser = null)
    {
        parent::__construct($translator, $validatorGuesser);
    }

    /**
     * {@inheritdoc}
     */
    public function transform(FormInterface $form, array $extensions = [], $widget = null)
    {
        $schema = [
            'type' => 'button',
            'title' => $form->getConfig()->getOption('label'),
        ];
        $schema = $this->addCommonSpecs($form, $schema, $extensions, $widget);

        return $schema;
    }
}
