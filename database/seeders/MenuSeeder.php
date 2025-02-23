<?php

namespace Database\Seeders;

use App\Models\Menus;
use App\Models\MenuItems;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        $menu = Menus::updateOrCreate(['name' => 'Admin'], [
            'name' => 'Admin',
            'status' => true,
            'system_reserve' => true
        ]);
        $menuItems = [
            [
                'label' => 'static.dashboard',
                'icon' => 'ri-dashboard-line',
                'route' => 'admin.dashboard.index',
                'permission' => '',
                'section' => 'static.home',
                'depth' => 0,
                'child' => []
            ],
            [
                'label' => 'static.users.users',
                'icon' => 'ri-group-line',
                'route' => 'admin.user.index',
                'permission' => 'user.index',
                'section' => 'static.user_management',
                'depth' => 0,
                'child' => [
                    [
                        'label' => 'static.users.all',
                        'icon' => 'ri-user-3-line',
                        'route' => 'admin.user.index',
                        'permission' => 'user.index',
                        'section' => 'static.user_management',
                        'depth' => 1,
                        'child' => []
                    ],
                    [
                        'label' => 'static.users.add',
                        'icon' => 'ri-user-add-line',
                        'route' => 'admin.user.create',
                        'permission' => 'user.create',
                        'section' => 'static.user_management',
                        'depth' => 1,
                        'child' => []
                    ],
                    [
                        'label' => 'static.users.role_permissions',
                        'icon' => 'ri-lock-line',
                        'route' => 'admin.role.index',
                        'permission' => 'role.index',
                        'section' => 'static.user_management',
                        'depth' => 1,
                        'child' => []
                    ]
                ]
            ],
            [
                'label' => 'static.media.media',
                'icon' => 'ri-folder-open-line',
                'section' => 'static.home',
                'route' => 'admin.media.index',
                'permission' => 'attachment.index',
                'depth' => 0,
                'child' => []
            ],
            [
                'label' => 'static.blogs.blogs',
                'icon' => 'ri-pushpin-line',
                'route' => 'admin.blog.index',
                'permission' => 'blog.index',
                'section' => 'static.content_management',
                'depth' => 0,
                'child' => [
                    [
                        'label' => 'static.blogs.all_blogs',
                        'icon' => 'ri-bookmark-line',
                        'route' => 'admin.blog.index',
                        'permission' => 'blog.index',
                        'section' => 'static.content_management',
                        'depth' => 1,
                        'child' => []
                    ],
                    [
                        'label' => 'static.blogs.add_blogs',
                        'icon' => 'ri-add-line',
                        'section' => 'static.content_management',
                        'route' => 'admin.blog.create',
                        'permission' => 'blog.create',
                        'depth' => 1,
                        'child' => []
                    ],
                    [
                        'label' => 'static.categories.categories',
                        'icon' => 'ri-folder-line',
                        'route' => 'admin.category.index',
                        'permission' => 'category.index',
                        'section' => 'static.content_management',
                        'depth' => 1,
                        'child' => []
                    ],
                    [
                        'label' => 'static.tags.tags',
                        'icon' => 'ri-price-tag-3-line',
                        'route' => 'admin.tag.index',
                        'section' => 'static.content_management',
                        'permission' => 'tag.index',
                        'depth' => 1,
                        'child' => []
                    ],
                ]
            ],
            [
                'label' => 'static.pages.pages',
                'icon' => 'ri-pages-line',
                'route' => 'admin.page.index',
                'permission' => 'page.index',
                'section' => 'static.content_management',
                'depth' => 0,
                'child' => [
                    [
                        'label' => 'static.pages.all_page',
                        'icon' => 'ri-list-check',
                        'route' => 'admin.page.index',
                        'permission' => 'page.index',
                        'section' => 'static.content_management',
                        'depth' => 1,
                        'child' => []
                    ],
                    [
                        'label' => 'static.pages.add',
                        'icon' => 'ri-add-line',
                        'route' => 'admin.page.create',
                        'permission' => 'page.create',
                        'section' => 'static.content_management',
                        'depth' => 1,
                        'child' => []
                    ],
                ]
            ],
            [
                'label' => 'static.notify_templates.notify_templates',
                'icon' => 'ri-pushpin-line',
                'route' => 'admin.email-template.index',
                'permission' => 'email_template.index',
                'section' => 'static.promotion_management',
                'depth' => 0,
                'child' => [
                    [
                        'label' => 'static.notify_templates.email',
                        'icon' => 'ri-dashboard-line',
                        'route' => 'admin.email-template.index',
                        'permission' => 'email_template.index',
                        'section' => 'static.promotion_management',
                        'depth' => 1,
                        'child' => []
                    ],
                    [
                        'label' => 'static.notify_templates.sms',
                        'icon' => 'ri-dashboard-line',
                        'route' => 'admin.sms-template.index',
                        'permission' => 'sms_template.index',
                        'section' => 'static.promotion_management',
                        'depth' => 1,
                        'child' => []
                    ],
                    [
                        'label' => 'static.notify_templates.push_notification',
                        'icon' => 'ri-dashboard-line',
                        'route' => 'admin.push-notification-template.index',
                        'permission' => 'push_notification_template.index',
                        'section' => 'static.promotion_management',
                        'depth' => 1,
                        'child' => []
                    ],
                ]
            ],
            [
                'label' => 'static.testimonials.testimonials',
                'icon' => 'ri-group-line',
                'route' => 'admin.testimonial.index',
                'permission' => 'testimonial.index',
                'section' => 'static.promotion_management',
                'depth' => 0,
                'child' => [
                    [
                        'label' => 'static.testimonials.all_testimonials',
                        'icon' => 'ri-list-check',
                        'route' => 'admin.testimonial.index',
                        'permission' => 'testimonial.index',
                        'section' => 'static.promotion_management',
                        'depth' => 1,
                        'child' => []
                    ],
                    [
                        'label' => 'static.testimonials.add',
                        'icon' => 'ri-add-line',
                        'route' => 'admin.testimonial.create',
                        'permission' => 'testimonial.create',
                        'section' => 'static.promotion_management',
                        'depth' => 1,
                        'child' => []
                    ],
                ]
            ],
            [
                'label' => 'static.faqs.faqs',
                'icon'  => 'ri-questionnaire-line',
                'route' => 'admin.faq.index',
                'permission' => 'faq.index',
                'section' => 'static.content_management',
                'depth' => 0,
                'child' => []
            ],
            [
                'label' => 'static.general_settings',
                'icon' => 'ri-settings-5-line',
                'route' => 'admin.setting.index',
                'permission' => 'setting.index',
                'section' => 'static.setting_management',
                'depth' => 0,
                'child' => [
                    [
                        'label' => 'static.languages.languages',
                        'icon'  => 'ri-translate-2',
                        'section' => 'static.setting_management',
                        'route' => 'admin.language.index',
                        'permission' => 'language.index',
                        'depth' => 1,
                        'child' => []
                    ],
                    [
                        'label' => 'static.taxes.taxes',
                        'icon'  => 'ri-percent-line',
                        'route' => 'admin.tax.index',
                        'permission' => 'tax.index',
                        'section' => 'static.financial_management',
                        'depth' => 1,
                        'child' => []
                    ],
                    [
                        'label' => 'static.currencies.currencies',
                        'icon'  => 'ri-currency-line',
                        'route' => 'admin.currency.index',
                        'permission' => 'currency.index',
                        'section' => 'static.financial_management',
                        'depth' => 1,
                        'child' => []
                    ],
                    [
                        'label' => 'static.plugins.plugins',
                        'icon'  => 'ri-plug-line',
                        'route' => 'admin.plugin.index',
                        'permission' => 'plugin.index',
                        'section' => 'static.setting_management',
                        'depth' => 1,
                        'child' => []
                    ],
                    [
                        'label' => 'static.payment_methods.payment_methods',
                        'icon'  => 'ri-secure-payment-line',
                        'route' => 'admin.payment-method.index',
                        'permission' => 'payment-method.index',
                        'section' => 'static.setting_management',
                        'depth' => 1,
                        'child' => []
                    ],
                    [
                        'label' => 'static.sms_gateways.sms_gateways',
                        'icon'  => 'ri-message-2-line',
                        'route' => 'admin.sms-gateway.index',
                        'permission' => 'sms-gateway.index',
                        'section' => 'static.setting_management',
                        'depth' => 1,
                        'child' => []
                    ],
                    [
                        'label' => 'static.systems.about',
                        'icon'  => 'ri-apps-line',
                        'route' => 'admin.about-system.index',
                        'permission' => 'about-system.index',
                        'section' => 'static.setting_management',
                        'depth' => 1,
                        'child' => []
                    ],
                    [
                        'label' => 'static.settings.settings',
                        'icon'  => 'ri-settings-5-line',
                        'route' => 'admin.setting.index',
                        'permission' => 'setting.index',
                        'section' => 'static.setting_management',
                        'depth' => 1,
                        'child' => []
                    ],
                ]
            ],
            [
                'label' => 'static.appearance.appearance',
                'icon' => 'ri-swap-3-line',
                'route' => 'admin.robot.index',
                'permission' => 'appearance.index',
                'section' => 'static.setting_management',
                'depth' => 0,
                'child' => [
                    [
                        'label' => 'static.appearance.robots',
                        'icon'  => '',
                        'route' => 'admin.robot.index',
                        'permission' => 'appearance.index',
                        'section' => 'static.setting_management',
                        'depth' => 1,
                        'child' => []
                    ],
                    [
                        'label' => 'static.landing_pages.landing_page_title',
                        'icon'  => 'ri-pages-line',
                        'route' => 'admin.landing-page.index',
                        'permission' => 'landing_page.index',
                        'section' => 'static.setting_management',
                        'depth' => 1,
                        'child' => []
                    ],
                    [
                        'label' => 'static.landing_pages.subscribers',
                        'icon'  => 'ri-pages-line',
                        'route' => 'admin.subscribes',
                        'permission' => 'landing_page.index',
                        'section' => 'static.setting_management',
                        'depth' => 1,
                        'child' => []
                    ],
                    [
                        'label' => 'static.appearance.customizations',
                        'icon'  => 'ri-pages-line',
                        'route' => 'admin.customization.index',
                        'permission' => 'appearance.index',
                        'section' => 'static.setting_management',
                        'depth' => 1,
                        'child' => []
                    ]
                ]
            ],
            [
                'label' => 'static.system_tools.system_tools',
                'icon' => 'ri-shield-user-line',
                'route' => 'admin.backup.index',
                'permission' => 'system-tool.index',
                'section' => 'static.setting_management',
                'depth' => 0,
                'child' => [
                    [
                        'label' => 'static.system_tools.backup',
                        'icon'  => '',
                        'route' => 'admin.backup.index',
                        'permission' => 'system-tool.index',
                        'section' => 'static.setting_management',
                        'depth' => 1,
                        'child' => []
                    ],
                    [
                        'label' => 'static.system_tools.activity_logs',
                        'icon'  => '',
                        'route' => 'admin.activity-logs.index',
                        'permission' => 'system-tool.index',
                        'section' => 'static.setting_management',
                        'depth' => 1,
                        'child' => []
                    ],
                    [
                        'label' => 'static.system_tools.database_cleanup',
                        'icon'  => '',
                        'route' => 'admin.cleanup-db.index',
                        'permission' => 'system-tool.index',
                        'section' => 'static.setting_management',
                        'depth' => 1,
                        'child' => []
                    ],
                ]
            ],
            [
                'label' => 'static.menus.menus',
                'icon' => 'ri-menu-2-line',
                'route' => 'admin.menu.index',
                'permission' => 'menu.index',
                'section' => 'static.setting_management',
                'depth' => 0,
                'child' => []
            ],

        ];

        $sort = 0;
        foreach ($menuItems as $menuItem) {
            $sort = $this->createOrUpdateMenuItem($menuItem, $sort, $menu->id);
            ++$sort;
        }
    }

    private function createOrUpdateMenuItem($menuItem, $sort, $menu, $parent = null)
    {
        $menuItemModel = MenuItems::updateOrCreate([
            'label' => $menuItem['label'],
            'icon' => $menuItem['icon'],
            'route' => $menuItem['route'],
            'permission' => $menuItem['permission'] ?? null,
            'parent' => $parent ? $parent->id : 0,
            'section' => $menuItem['section'],
            'depth' => $menuItem['depth'],
            'sort' => $sort,
            'menu' => $menu
        ]);

        if (count($menuItem['child'])) {
            foreach ($menuItem['child'] as $childMenuItem) {
                $sortIndex = ++$sort;
                $this->createOrUpdateMenuItem($childMenuItem, $sortIndex, $menu, $menuItemModel);
            }

            $sort = $sortIndex;
        }

        return $sort;
    }
}
