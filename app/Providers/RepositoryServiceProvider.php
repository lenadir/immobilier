<?php

namespace App\Providers;

use App\Repositories\Contracts\ImageRepositoryInterface;
use App\Repositories\Contracts\PropertyRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\ImageRepository;
use App\Repositories\PropertyRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

/**
 * Lie les interfaces des repositories à leurs implémentations concrètes.
 * Permet l'injection de dépendances dans les Services et Controllers.
 */
class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(PropertyRepositoryInterface::class, PropertyRepository::class);
        $this->app->bind(UserRepositoryInterface::class,     UserRepository::class);
        $this->app->bind(ImageRepositoryInterface::class,    ImageRepository::class);
    }
}
