<?php

namespace Botble\Testimonial\Providers;

use Botble\Base\Facades\DashboardMenu;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\LanguageAdvanced\Supports\LanguageAdvancedManager;
use Botble\Testimonial\Models\Testimonial;
use Botble\Testimonial\Repositories\Caches\TestimonialCacheDecorator;
use Botble\Testimonial\Repositories\Eloquent\TestimonialRepository;
use Botble\Testimonial\Repositories\Interfaces\TestimonialInterface;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Support\ServiceProvider;

class TestimonialServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register(): void
    {
        $this->app->bind(TestimonialInterface::class, function () {
            return new TestimonialCacheDecorator(new TestimonialRepository(new Testimonial()));
        });
    }

    public function boot(): void
    {
        $this->setNamespace('plugins/testimonial')
            ->loadHelpers()
            ->loadAndPublishConfigurations(['permissions'])
            ->loadMigrations()
            ->loadAndPublishTranslations()
            ->loadRoutes();

        if (defined('LANGUAGE_ADVANCED_MODULE_SCREEN_NAME')) {
            LanguageAdvancedManager::registerModule(Testimonial::class, [
                'name',
                'content',
                'company',
            ]);
        }

        $this->app['events']->listen(RouteMatched::class, function () {
            DashboardMenu::registerItem([
                'id' => 'cms-plugins-testimonial',
                'priority' => 5,
                'parent_id' => null,
                'name' => 'plugins/testimonial::testimonial.name',
                'icon' => 'far fa-comment-dots',
                'url' => route('testimonial.index'),
                'permissions' => ['testimonial.index'],
            ]);
        });
    }
}
