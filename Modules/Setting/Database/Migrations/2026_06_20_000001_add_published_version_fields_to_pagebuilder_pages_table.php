<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Setting\Entities\Page;

return new class extends Migration
{
    public function up(): void
    {
        // Schema::table('pagebuilder_pages', function (Blueprint $table) {
        //     $table->jsonb('published_content')->nullable()->after('root');
        //     $table->jsonb('published_root')->nullable()->after('published_content');
        //     $table->string('published_title', 255)->nullable()->after('published_root');
        //     $table->string('published_slug', 255)->nullable()->after('published_title');

        //     $table->index('published_slug');
        // });

        // $publishedPages = DB::table('pagebuilder_pages')
        //     ->where('status', Page::STATUS_PUBLISHED)
        //     ->get(['id', 'title', 'slug', 'content', 'root']);

        // foreach ($publishedPages as $page) {
        //     DB::table('pagebuilder_pages')
        //         ->where('id', $page->id)
        //         ->update([
        //             'published_content' => $page->content,
        //             'published_root' => $page->root,
        //             'published_title' => $page->title,
        //             'published_slug' => $page->slug,
        //         ]);
        // }
    }

    public function down(): void
    {
        Schema::table('pagebuilder_pages', function (Blueprint $table) {
            $table->dropIndex(['published_slug']);
            $table->dropColumn([
                'published_content',
                'published_root',
                'published_title',
                'published_slug',
            ]);
        });
    }
};
