<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Book;
use App\Models\Post;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index()
    {
        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>';
        $sitemap .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        
        // Main sitemap
        $sitemap .= '<sitemap>';
        $sitemap .= '<loc>' . route('sitemap.main') . '</loc>';
        $sitemap .= '<lastmod>' . now()->toISOString() . '</lastmod>';
        $sitemap .= '</sitemap>';
        
        // Users sitemap
        $sitemap .= '<sitemap>';
        $sitemap .= '<loc>' . route('sitemap.users') . '</loc>';
        $sitemap .= '<lastmod>' . (User::latest()->first()?->updated_at?->toISOString() ?? now()->toISOString()) . '</lastmod>';
        $sitemap .= '</sitemap>';
        
        // Books sitemap
        $sitemap .= '<sitemap>';
        $sitemap .= '<loc>' . route('sitemap.books') . '</loc>';
        $sitemap .= '<lastmod>' . (Book::latest()->first()?->updated_at?->toISOString() ?? now()->toISOString()) . '</lastmod>';
        $sitemap .= '</sitemap>';
        
        // Posts sitemap
        $sitemap .= '<sitemap>';
        $sitemap .= '<loc>' . route('sitemap.posts') . '</loc>';
        $sitemap .= '<lastmod>' . (Post::latest()->first()?->updated_at?->toISOString() ?? now()->toISOString()) . '</lastmod>';
        $sitemap .= '</sitemap>';
        
        $sitemap .= '</sitemapindex>';
        
        return response($sitemap, 200, [
            'Content-Type' => 'application/xml'
        ]);
    }
    
    public function main()
    {
        $urls = [
            [
                'url' => url('/'),
                'lastmod' => now()->toISOString(),
                'changefreq' => 'daily',
                'priority' => '1.0'
            ],
            [
                'url' => route('search.index'),
                'lastmod' => now()->toISOString(),
                'changefreq' => 'weekly',
                'priority' => '0.8'
            ],
            [
                'url' => route('book.index'),
                'lastmod' => now()->toISOString(),
                'changefreq' => 'weekly',
                'priority' => '0.9'
            ],
            [
                'url' => route('user.index'),
                'lastmod' => now()->toISOString(),
                'changefreq' => 'weekly',
                'priority' => '0.7'
            ]
        ];
        
        return $this->generateSitemap($urls);
    }
    
    public function users()
    {
        $users = User::withoutGlobalScopes()
            ->whereNotNull('username')
            ->orWhere('id', '>', 0)
            ->select(['id', 'username', 'updated_at'])
            ->get();
        
        $urls = [];
        foreach ($users as $user) {
            $urls[] = [
                'url' => route('profile', $user->username ?? $user->id),
                'lastmod' => $user->updated_at->toISOString(),
                'changefreq' => 'weekly',
                'priority' => '0.6'
            ];
        }
        
        return $this->generateSitemap($urls);
    }
    
    public function books()
    {
        $books = Book::select(['id', 'title', 'updated_at'])->get();
        
        $urls = [];
        foreach ($books as $book) {
            $urls[] = [
                'url' => route('book.show', $book),
                'lastmod' => $book->updated_at->toISOString(),
                'changefreq' => 'monthly',
                'priority' => '0.7'
            ];
        }
        
        return $this->generateSitemap($urls);
    }
    
    public function posts()
    {
        $posts = Post::where('status', 'published')
            ->select(['id', 'updated_at'])
            ->get();
        
        $urls = [];
        foreach ($posts as $post) {
            $urls[] = [
                'url' => url("/posts/{$post->id}"),
                'lastmod' => $post->updated_at->toISOString(),
                'changefreq' => 'monthly',
                'priority' => '0.5'
            ];
        }
        
        return $this->generateSitemap($urls);
    }
    
    private function generateSitemap(array $urls)
    {
        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>';
        $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        
        foreach ($urls as $url) {
            $sitemap .= '<url>';
            $sitemap .= '<loc>' . htmlspecialchars($url['url']) . '</loc>';
            $sitemap .= '<lastmod>' . $url['lastmod'] . '</lastmod>';
            $sitemap .= '<changefreq>' . $url['changefreq'] . '</changefreq>';
            $sitemap .= '<priority>' . $url['priority'] . '</priority>';
            $sitemap .= '</url>';
        }
        
        $sitemap .= '</urlset>';
        
        return response($sitemap, 200, [
            'Content-Type' => 'application/xml'
        ]);
    }
}