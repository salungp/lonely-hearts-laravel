<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use App\Models\Ad;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';
    protected $description = 'Generate sitemap.xml file';

    public function handle()
    {
        $sitemap = Sitemap::create()
            ->add(Url::create('/')
                ->setPriority(1.0)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY));

        // Static pages
        $pages = ['/about', '/privacy-policy', '/what-is-lonely-hearts', '/terms-of-service', '/how-it-works', '/help'];
        foreach ($pages as $page) {
            $sitemap->add(Url::create($page)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY));
        }

        // Ads
        $ads = Ad::all();
        foreach ($ads as $ad) {
            $sitemap->add(
                Url::create("/ads/{$ad->id}")
                    ->setLastModificationDate($ad->updated_at)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                    ->setPriority(0.7)
            );
        }

        $sitemap->writeToFile(public_path('sitemap.xml'));

        $this->info('✅ Sitemap generated successfully at public/sitemap.xml');
    }
}
