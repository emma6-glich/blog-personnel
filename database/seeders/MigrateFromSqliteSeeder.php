<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MigrateFromSqliteSeeder extends Seeder
{
    public function run(): void
    {
        // Connexion SQLite
        config(['database.connections.sqlite_source' => [
            'driver'   => 'sqlite',
            'database' => database_path('database.sqlite'),
            'prefix'   => '',
        ]]);

        $sqlite = DB::connection('sqlite_source');

        // Vider les tables dans le bon ordre
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('post_views')->truncate();
        DB::table('reactions')->truncate();
        DB::table('comments')->truncate();
        DB::table('posts')->truncate();
        DB::table('categories')->truncate();
        DB::table('users')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // ===== USERS =====
        $users = $sqlite->table('users')->get();
        foreach ($users as $user) {
            DB::table('users')->updateOrInsert(
                ['email' => $user->email],
                [
                    'id'                => $user->id,
                    'name'              => $user->name,
                    'email'             => $user->email,
                    'password'          => $user->password,
                    'email_verified_at' => $user->email_verified_at,
                    'remember_token'    => $user->remember_token,
                    'created_at'        => $user->created_at,
                    'updated_at'        => $user->updated_at,
                ]
            );
        }
        echo "✅ Users transférés : " . count($users) . "\n";

        // ===== CATEGORIES =====
        $categories = $sqlite->table('categories')->get();
        foreach ($categories as $cat) {
            DB::table('categories')->updateOrInsert(
                ['id' => $cat->id],
                [
                    'id'         => $cat->id,
                    'name'       => $cat->name,
                    'slug'       => $cat->slug,
                    'created_at' => $cat->created_at,
                    'updated_at' => $cat->updated_at,
                ]
            );
        }
        echo "✅ Catégories transférées : " . count($categories) . "\n";

        // ===== POSTS =====
        $posts = $sqlite->table('posts')->get();
        foreach ($posts as $post) {
            DB::table('posts')->updateOrInsert(
                ['id' => $post->id],
                (array) $post
            );
        }
        echo "✅ Articles transférés : " . count($posts) . "\n";

        // ===== COMMENTS =====
        $comments = $sqlite->table('comments')->get();
        foreach ($comments as $comment) {
            DB::table('comments')->updateOrInsert(
                ['id' => $comment->id],
                (array) $comment
            );
        }
        echo "✅ Commentaires transférés : " . count($comments) . "\n";

        // ===== REACTIONS =====
        try {
            $reactions = $sqlite->table('reactions')->get();
            foreach ($reactions as $reaction) {
                DB::table('reactions')->updateOrInsert(
                    ['id' => $reaction->id],
                    (array) $reaction
                );
            }
            echo "✅ Réactions transférées : " . count($reactions) . "\n";
        } catch (\Exception $e) {
            echo "⚠️ Réactions : " . $e->getMessage() . "\n";
        }

        // ===== POST VIEWS =====
        try {
            $views = $sqlite->table('post_views')->get();
            foreach ($views as $view) {
                DB::table('post_views')->updateOrInsert(
                    ['id' => $view->id],
                    (array) $view
                );
            }
            echo "✅ Vues transférées : " . count($views) . "\n";
        } catch (\Exception $e) {
            echo "⚠️ Vues : " . $e->getMessage() . "\n";
        }

        echo "\n🎉 Migration SQLite → MySQL terminée !\n";
    }
}
