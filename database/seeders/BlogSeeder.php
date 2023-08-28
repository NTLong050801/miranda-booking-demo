<?php

namespace Database\Seeders;

use Botble\ACL\Models\User;
use Botble\Base\Models\MetaBox as MetaBoxModel;
use Botble\Base\Supports\BaseSeeder;
use Botble\Blog\Models\Category;
use Botble\Blog\Models\Post;
use Botble\Blog\Models\Tag;
use Botble\Language\Models\LanguageMeta;
use Botble\Slug\Models\Slug;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Botble\Slug\Facades\SlugHelper;

class BlogSeeder extends BaseSeeder
{
    public function run(): void
    {
        $this->uploadFiles('news');

        Post::query()->truncate();
        Category::query()->truncate();
        Tag::query()->truncate();
        DB::table('posts_translations')->truncate();
        DB::table('categories_translations')->truncate();
        DB::table('tags_translations')->truncate();
        Slug::query()->where('reference_type', Post::class)->delete();
        Slug::query()->where('reference_type', Tag::class)->delete();
        Slug::query()->where('reference_type', Category::class)->delete();
        MetaBoxModel::query()->where('reference_type', Post::class)->delete();
        MetaBoxModel::query()->where('reference_type', Tag::class)->delete();
        MetaBoxModel::query()->where('reference_type', Category::class)->delete();
        LanguageMeta::query()->where('reference_type', Post::class)->delete();
        LanguageMeta::query()->where('reference_type', Tag::class)->delete();
        LanguageMeta::query()->where('reference_type', Category::class)->delete();

        $categories = [
            [
                'name' => 'General',
                'is_default' => true,
            ],
            [
                'name' => 'Hotel',
            ],
            [
                'name' => 'Booking',
            ],
            [
                'name' => 'Resort',
            ],
            [
                'name' => 'Travel',
            ],
        ];

        foreach ($categories as $index => $item) {
            $this->createCategory(Arr::except($item, 'children'), 0, $index != 0);
        }

        $tags = [
            [
                'name' => 'General',
            ],
            [
                'name' => 'Hotel',
            ],
            [
                'name' => 'Booking',
            ],
            [
                'name' => 'Resort',
            ],
            [
                'name' => 'Travel',
            ],
        ];

        foreach ($tags as $item) {
            $item['author_id'] = 1;
            $item['author_type'] = User::class;
            $tag = Tag::query()->create($item);

            Slug::query()->create([
                'reference_type' => Tag::class,
                'reference_id' => $tag->id,
                'key' => Str::slug($tag->name),
                'prefix' => SlugHelper::getPrefix(Tag::class),
            ]);
        }

        $posts = [
            [
                'name' => 'Each of our 8 double rooms has its own distinct.',
            ],
            [
                'name' => 'Essential Qualities of Highly Successful Music',
            ],
            [
                'name' => '9 Things I Love About Shaving My Head',
            ],
            [
                'name' => 'Why Teamwork Really Makes The Dream Work',
            ],
            [
                'name' => 'The World Caters to Average People',
            ],
            [
                'name' => 'The litigants on the screen are not actors',
            ],
        ];

        foreach ($posts as $index => $item) {
            $item['content'] = '<p>I have seen many people underestimating the power of their wallets. To them, they are just a functional item they use to carry. As a result, they often end up with the wallets which are not really suitable for them.</p>

<p>You should pay more attention when you choose your wallets. There are a lot of them on the market with the different designs and styles. When you choose carefully, you would be able to buy a wallet that is catered to your needs. Not to mention that it will help to enhance your style significantly.</p>

<p style="text-align:center"><img alt="f4" src="/storage/news/04.jpg" /></p>

<p><br />
&nbsp;</p>

<p><strong><em>For all of the reason above, here are 7 expert tips to help you pick up the right men&rsquo;s wallet for you:</em></strong></p>

<h4><strong>Number 1: Choose A Neat Wallet</strong></h4>

<p>The wallet is an essential accessory that you should go simple. Simplicity is the best in this case. A simple and neat wallet with the plain color and even&nbsp;<strong>minimalist style</strong>&nbsp;is versatile. It can be used for both formal and casual events. In addition, that wallet will go well with most of the clothes in your wardrobe.</p>

<p>Keep in mind that a wallet will tell other people about your personality and your fashion sense as much as other clothes you put on. Hence, don&rsquo;t go cheesy on your wallet or else people will think that you have a funny and particular style.</p>

<p style="text-align:center"><img alt="f5" src="/storage/news/05.jpg" /></p>

<p><br />
&nbsp;</p>
<hr />
<h4><strong>Number 2: Choose The Right Size For Your Wallet</strong></h4>

<p>You should avoid having an over-sized wallet. Don&rsquo;t think that you need to buy a big wallet because you have a lot to carry with you. In addition, a fat wallet is very ugly. It will make it harder for you to slide the wallet into your trousers&rsquo; pocket. In addition, it will create a bulge and ruin your look.</p>

<p>Before you go on to buy a new wallet, clean out your wallet and place all of the items from your wallet on a table. Throw away things that you would never need any more such as the old bills or the expired gift cards. Remember to check your wallet on a frequent basis to get rid of all of the old stuff that you don&rsquo;t need anymore.</p>

<p style="text-align:center"><img alt="f1" src="/storage/news/06.jpg" /></p>

<p><br />
&nbsp;</p>

<hr />
<h4><strong>Number 3: Don&rsquo;t Limit Your Options Of Materials</strong></h4>

<p>The types and designs of wallets are not the only things that you should consider when you go out searching for your best wallet. You have more than 1 option of material rather than leather to choose from as well.</p>

<p>You can experiment with other available options such as cotton, polyester and canvas. They all have their own pros and cons. As a result, they will be suitable for different needs and requirements. You should think about them all in order to choose the material which you would like the most.</p>

<p>&nbsp;</p>
';

            $item['author_id'] = 1;
            $item['author_type'] = User::class;
            $item['views'] = fake()->numberBetween(100, 2500);
            $item['is_featured'] = true;
            $item['image'] = 'news/0' . ($index + 1) . '.jpg';
            $item['description'] = 'You should pay more attention when you choose your wallets. There are a lot of them on the market with the different designs and styles. When you choose carefully, you would be able to buy a wallet that is catered to your needs. Not to mention that it will help to enhance your style significantly.';
            $item['content'] = str_replace(url(''), '', $item['content']);

            $post = Post::query()->create($item);

            $post->categories()->sync([
                fake()->numberBetween(1, 2),
                fake()->numberBetween(3, 4),
            ]);

            $post->tags()->sync([1, 2, 3, 4, 5]);

            Slug::query()->create([
                'reference_type' => Post::class,
                'reference_id' => $post->id,
                'key' => Str::slug($post->name),
                'prefix' => SlugHelper::getPrefix(Post::class),
            ]);
        }

        $translations = [
            [
                'name' => 'Chung',
            ],
            [
                'name' => 'Khách sạn',
            ],
            [
                'name' => 'Đặt phòng',
            ],
            [
                'name' => 'Khu nghỉ dưỡng',
            ],
            [
                'name' => 'Du lịch',
            ],
        ];

        foreach ($translations as $index => $item) {
            $item['lang_code'] = 'vi';
            $item['categories_id'] = Category::skip($index)->value('id');

            DB::table('categories_translations')->insert($item);
        }

        $translations = [
            [
                'name' => 'Chung',
            ],
            [
                'name' => 'Khách sạn',
            ],
            [
                'name' => 'Đặt phòng',
            ],
            [
                'name' => 'Khu nghỉ dưỡng',
            ],
            [
                'name' => 'Du lịch',
            ],
        ];

        foreach ($translations as $index => $item) {
            $item['lang_code'] = 'vi';
            $item['tags_id'] = Tag::skip($index)->value('id');

            DB::table('tags_translations')->insert($item);
        }

        $translations = [
            [
                'name' => 'Mỗi phòng trong số 8 phòng đôi của chúng tôi có sự khác biệt riêng.',
            ],
            [
                'name' => 'Những phẩm chất cần thiết của âm nhạc thành công cao',
            ],
            [
                'name' => '9 điều tôi thích khi cạo đầu',
            ],
            [
                'name' => 'Tại sao làm việc theo nhóm thực sự biến giấc mơ thành công',
            ],
            [
                'name' => 'Thế giới phục vụ cho những người trung bình',
            ],
            [
                'name' => 'Các đương sự trên màn hình không phải là diễn viên',
            ],
        ];

        foreach ($translations as $index => $item) {
            $item['lang_code'] = 'vi';

            $post = Post::skip($index)->first();

            $item['posts_id'] = $post->id;
            $item['description'] = $post->description;
            $item['content'] = $post->content;

            DB::table('posts_translations')->insert($item);
        }
    }

    protected function createCategory(
        array $item,
        int|string $parentId = 0,
        bool $isFeatured = false
    ) {
        $item['description'] = fake()->text();
        $item['author_id'] = 1;
        $item['parent_id'] = $parentId;
        $item['is_featured'] = $isFeatured;

        $category = Category::query()->create($item);

        Slug::query()->create([
            'reference_type' => Category::class,
            'reference_id' => $category->id,
            'key' => Str::slug($category->name),
            'prefix' => SlugHelper::getPrefix(Category::class),
        ]);

        return $category;
    }
}
