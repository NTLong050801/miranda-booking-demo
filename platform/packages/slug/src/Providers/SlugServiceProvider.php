<?php

namespace Botble\Slug\Providers;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Facades\DashboardMenu;
use Botble\Base\Facades\MacroableModels;
use Botble\Base\Models\BaseModel;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\Page\Models\Page;
use Botble\Slug\Facades\SlugHelper as SlugHelperFacade;
use Botble\Slug\Models\Slug;
use Botble\Slug\Repositories\Caches\SlugCacheDecorator;
use Botble\Slug\Repositories\Eloquent\SlugRepository;
use Botble\Slug\Repositories\Interfaces\SlugInterface;
use Botble\Slug\SlugCompiler;
use Botble\Slug\SlugHelper;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Support\ServiceProvider;

class SlugServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    protected bool $defer = true;

    public function register(): void
    {
        $this->app->bind(SlugInterface::class, function () {
            return new SlugCacheDecorator(new SlugRepository(new Slug()));
        });

        $this->app->singleton(SlugHelper::class, function () {
            return new SlugHelper(new SlugCompiler());
        });
    }

    public function boot(): void
    {
        $this
            ->setNamespace('packages/slug')
            ->loadAndPublishConfigurations(['general'])
            ->loadHelpers()
            ->loadAndPublishViews()
            ->loadRoutes()
            ->loadAndPublishTranslations()
            ->loadMigrations()
            ->publishAssets();

        $this->app->register(EventServiceProvider::class);
        $this->app->register(CommandServiceProvider::class);

        $this->app['events']->listen(RouteMatched::class, function () {
            DashboardMenu::registerItem([
                'id' => 'cms-packages-slug-permalink',
                'priority' => 5,
                'parent_id' => 'cms-core-settings',
                'name' => 'packages/slug::slug.permalink_settings',
                'icon' => null,
                'url' => route('slug.settings'),
                'permissions' => ['settings.options'],
            ]);
        });

        $this->app->booted(function () {
            $this->app->register(FormServiceProvider::class);

            foreach (array_keys($this->app->make(SlugHelper::class)->supportedModels()) as $item) {
                if (! class_exists($item)) {
                    continue;
                }

                /**
                 * @var BaseModel $item
                 */
                $item::resolveRelationUsing('slugable', function ($model) {
                    return $model->morphOne(Slug::class, 'reference')->select([
                        'id',
                        'key',
                        'reference_type',
                        'reference_id',
                        'prefix',
                    ]);
                });

                MacroableModels::addMacro($item, 'getSlugAttribute', function () {
                    /**
                     * @var BaseModel $this
                     */
                    return $this->slugable ? $this->slugable->key : '';
                });

                MacroableModels::addMacro($item, 'getSlugIdAttribute', function () {
                    /**
                     * @var BaseModel $this
                     */
                    return $this->slugable ? $this->slugable->getKey() : '';
                });

                MacroableModels::addMacro(
                    $item,
                    'getUrlAttribute',
                    function () {
                        /**
                         * @var BaseModel $this
                         */
                        if (! $this->slug || (get_class($this) == Page::class && BaseHelper::isHomepage($this->getKey()))) {
                            return route('public.index');
                        }

                        $prefix = $this->slugable ? $this->slugable->prefix : null;
                        $prefix = apply_filters(FILTER_SLUG_PREFIX, $prefix);

                        $prefix = SlugHelperFacade::getTranslator()->compile($prefix, $this);

                        return apply_filters(
                            'slug_filter_url',
                            url($prefix ? $prefix . '/' . $this->slug : $this->slug) . SlugHelperFacade::getPublicSingleEndingURL()
                        );
                    }
                );
            }

            $this->app->register(HookServiceProvider::class);
        });
    }

    public function provides(): array
    {
        return [
            SlugHelper::class,
        ];
    }
}
