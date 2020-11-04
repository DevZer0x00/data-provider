<?php

namespace DevZer0x00\DataProvider\Traits;

use DevZer0x00\DataProvider\Exception\ConfigException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\OptionsResolver\Exception\ExceptionInterface;

trait ConfigurableTrait
{
    public function __construct(array $config = [])
    {
        try {
            $config = $this->configureOptions(new OptionsResolver())
                ->resolve($config);
        } catch (ExceptionInterface $e) {
            throw new ConfigException('Invalid config options', 0, $e);
        }

        $this->configure($config);
    }

    protected function configure(array $config): void
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        foreach ($config as $name => $value) {
            $propertyAccessor->setValue($this, $name, $value);
        }
    }

    abstract protected function configureOptions(OptionsResolver $resolver): OptionsResolver;
}