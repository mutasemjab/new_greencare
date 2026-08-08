<?php

namespace Database\Seeders;

use App\Models\ForumPost;
use App\Models\ForumReply;
use App\Models\ForumSubCategory;
use App\Models\User;
use Illuminate\Database\Seeder;

class ForumPostSeeder extends Seeder
{
    public function run(): void
    {
        $mothers = $this->seedMotherUsers();

        $posts = [
            'متابعة الحمل' => [
                [
                    'type'    => 'question',
                    'title'   => 'هل الشعور بالتعب الشديد بالشهر الأول طبيعي؟',
                    'content' => 'أنا بالأسبوع السادس من الحمل وحاسة بتعب وغثيان طول اليوم، هل هاد طبيعي وإلى متى بيستمر؟',
                    'replies' => [
                        'أهلين، هاد طبيعي جدًا بسبب التغيرات الهرمونية وبيتحسن غالبًا بعد الشهر الثالث. بس لو التعب أثر على أكلك وشربك لازم تراجعي الطبيب.',
                        'صار معي نفس الشي، خفّ كثير بعد الأسبوع الثاني عشر. حاولي تاكلي وجبات صغيرة ومتكررة.',
                    ],
                ],
                [
                    'type'    => 'experience',
                    'title'   => 'تجربتي مع المتابعة الدورية للحمل',
                    'content' => 'حبيت شارككم تجربتي، الالتزام بمواعيد الفحوصات كل شهر ساعدني كثير أطمن على وضعي ووضع الجنين، وأنصح كل حامل ما تتأخر بالمواعيد حتى لو حاسة أنها بخير.',
                    'replies' => [
                        'كلامك صحيح، أنا كمان كنت مقصرة بالمتابعة وندمت، هلق صرت التزم من أول الحمل.',
                    ],
                ],
            ],
            'ما بعد الولادة' => [
                [
                    'type'    => 'question',
                    'title'   => 'كم مدة النفاس الطبيعية؟',
                    'content' => 'ولدت من أسبوعين ولسا في نزيف خفيف، هل هاد طبيعي؟',
                    'replies' => [
                        'النفاس بيستمر عادة من 4 إلى 6 أسابيع، بس إذا النزيف زاد أو تغيّر لونه لازم تراجعي الطبيب فورًا.',
                    ],
                ],
            ],
            'التغذية' => [
                [
                    'type'    => 'question',
                    'title'   => 'أفضل وقت لإدخال الأطعمة الصلبة للرضيع؟',
                    'content' => 'طفلي عمره 5 شهور، متى المفروض أبدأ أدخله أكل صلب؟',
                    'replies' => [
                        'عادة ينصح بالبدء بعد إتمام الشهر السادس، استشيري طبيب الأطفال المتابع لحالة صغيرك قبل البدء.',
                    ],
                ],
            ],
            'اللقاحات' => [
                [
                    'type'    => 'experience',
                    'title'   => 'جدول اللقاحات ساعدني كثير بالتنظيم',
                    'content' => 'كنت دايمًا ناسية مواعيد اللقاحات، لحد ما صرت أستخدم تذكير بالتطبيق، بينصح فيه كل أم تتابع الجدول بدقة.',
                    'replies' => [],
                ],
            ],
            'النوم' => [
                [
                    'type'    => 'question',
                    'title'   => 'كيف أنظم روتين نوم لطفلي الرضيع؟',
                    'content' => 'طفلي عمره 3 شهور ونومه غير منتظم إطلاقًا، أي نصائح؟',
                    'replies' => [
                        'حاولي تثبتي روتين ثابت قبل النوم (حمام، ترضيع، تهدئة) بنفس الوقت كل يوم، بياخد وقت بس بينظم لاحقًا.',
                    ],
                ],
            ],
        ];

        foreach ($posts as $subCategoryName => $subPosts) {
            $subCategory = ForumSubCategory::where('name', $subCategoryName)->first();

            if (!$subCategory) {
                continue;
            }

            foreach ($subPosts as $i => $postData) {
                $author = $mothers[$i % count($mothers)];

                $post = ForumPost::firstOrCreate(
                    ['title' => $postData['title']],
                    [
                        'user_id'               => $author->id,
                        'forum_sub_category_id' => $subCategory->id,
                        'type'                  => $postData['type'],
                        'content'               => $postData['content'],
                        'is_active'             => true,
                    ]
                );

                if ($post->wasRecentlyCreated) {
                    foreach ($postData['replies'] as $j => $replyContent) {
                        $replyAuthor = $mothers[($i + $j + 1) % count($mothers)];

                        ForumReply::create([
                            'forum_post_id' => $post->id,
                            'user_id'       => $replyAuthor->id,
                            'content'       => $replyContent,
                            'is_active'     => true,
                        ]);
                    }
                }
            }
        }
    }

    private function seedMotherUsers(): array
    {
        $names = [
            ['name' => 'سارة العلي',   'phone' => '0790000101'],
            ['name' => 'رنا الخطيب',   'phone' => '0790000102'],
            ['name' => 'هبة النابلسي', 'phone' => '0790000103'],
            ['name' => 'ديمة قاسم',    'phone' => '0790000104'],
        ];

        return array_map(function ($data) {
            return User::firstOrCreate(
                ['phone' => $data['phone']],
                [
                    'name'      => $data['name'],
                    'role'      => 'patient',
                    'is_active' => true,
                ]
            );
        }, $names);
    }
}
