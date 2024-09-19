<?php

namespace App\Core\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;

abstract class AbstractFixture extends Fixture
{
    protected string $env;

    public function __construct(string $env)
    {
        $this->env = $env;
    }

    public function getData(): array
    {
        return 'test' === $this->env ? $this->getTestData() : $this->getDevData();
    }

    public function getTestData(): array
    {
        return [];
    }

    public function getDevData(): array
    {
        return [];
    }
}
