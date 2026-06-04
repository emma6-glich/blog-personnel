<?php

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        $posts = [
            [
                'title'       => 'Pourquoi j\'ai choisi Laravel pour mes projets web',
                'slug'        => 'pourquoi-laravel-mes-projets-web',
                'content'     => "Quand j'ai commencé le développement web, j'ai essayé plusieurs frameworks : CodeIgniter, Symfony, et finalement Laravel. Ce qui m'a convaincu définitivement c'est son élégance et sa simplicité.\n\nLaravel propose une syntaxe claire et intuitive qui permet de construire des applications robustes en un temps record. L'ORM Eloquent rend les interactions avec la base de données presque naturelles, et le système de migrations permet de versionner sa base de données comme du code.\n\nAujourd'hui, tous mes projets web sont construits avec Laravel. C'est un choix que je ne regrette absolument pas.",
                'category_id' => 1,
                'user_id'     => 1,
            ],
            [
                'title'       => 'Les outils indispensables de mon setup de développeur',
                'slug'        => 'outils-indispensables-setup-developpeur',
                'content'     => "Après plusieurs années de développement, j'ai affiné mon setup pour être le plus productif possible. Voici mes outils du quotidien.\n\nVS Code est mon éditeur principal. Avec les bonnes extensions (PHP Intelephense, Tailwind CSS IntelliSense, GitLens), il devient une vraie centrale de productivité.\n\nPour la gestion des versions, Git est évidemment incontournable. Je travaille avec GitHub pour tous mes projets, qu'ils soient personnels ou professionnels.\n\nEnfin, Figma pour les maquettes avant de coder. Prendre le temps de designer avant de développer m'évite de nombreux allers-retours.",
                'category_id' => 1,
                'user_id'     => 1,
            ],
            [
                'title'       => 'Mon premier voyage en solo : leçons apprises',
                'slug'        => 'premier-voyage-solo-lecons-apprises',
                'content'     => "Partir seul pour la première fois fait peur. On imagine mille scénarios catastrophes. Mais c'est aussi l'une des expériences les plus enrichissantes qu'on puisse vivre.\n\nJ'ai appris à faire confiance aux étrangers, à m'adapter à l'imprévu, et surtout à me connaître moi-même. Quand on voyage seul, on prend toutes les décisions, on gère tous les imprévus, et on en ressort grandi.\n\nMon conseil : commencez par une destination accessible, pas trop loin, avec une langue que vous comprenez un minimum. Puis élargissez progressivement votre zone de confort.",
                'category_id' => 2,
                'user_id'     => 1,
            ],
            [
                'title'       => 'Comment bien préparer son sac pour un long voyage',
                'slug'        => 'preparer-sac-long-voyage',
                'content'     => "La règle d'or du voyageur expérimenté : toujours prendre moins que ce qu'on pense avoir besoin. On emporte toujours trop, et on regrette chaque kilo superflu dès le premier aéroport.\n\nMon kit essentiel : des vêtements polyvalents qui se mélangent facilement, une trousse de toilette minimaliste, un adaptateur universel, une batterie externe, et une bonne paire d'écouteurs.\n\nPour les documents, je garde toujours une copie numérique dans le cloud et une copie papier séparée de l'original. Cette habitude m'a sauvé la mise une fois.",
                'category_id' => 2,
                'user_id'     => 1,
            ],
            [
                'title'       => 'Comment j\'ai appris à coder seule depuis chez moi',
                'slug'        => 'apprendre-coder-seule-maison',
                'content'     => "Beaucoup me demandent comment j'ai appris à développer sans école spécialisée. La réponse est simple : internet, de la discipline, et beaucoup d'échecs.\n\nJ'ai commencé par HTML et CSS en suivant des tutoriels gratuits sur YouTube. Puis j'ai appris PHP, et progressivement je suis arrivée à Laravel. Chaque étape a pris du temps, mais chaque petit projet réalisé m'a donné l'envie de continuer.\n\nLe secret ? Construire des projets concrets dès le début. Pas juste suivre des cours. Coder un vrai blog, une vraie application, même imparfaite. C'est comme ça qu'on apprend vraiment.",
                'category_id' => 3,
                'user_id'     => 1,
            ],
            [
                'title'       => 'Pourquoi j\'ai créé ce blog et ce que j\'espère en faire',
                'slug'        => 'pourquoi-creer-ce-blog',
                'content'     => "Ce blog est né d'un besoin simple : avoir un espace à moi pour partager ce que j'apprends, ce que je vis, et ce qui m'inspire.\n\nJe ne prétends pas être experte en quoi que ce soit. Je suis juste quelqu'un qui apprend en permanence et qui trouve que partager ce chemin peut être utile à d'autres.\n\nMes trois univers — la technologie, le voyage et le personnel — semblent peut-être sans lien. Mais ils ont tous un point commun : la curiosité. La curiosité d'explorer de nouveaux outils, de nouveaux endroits, et de mieux se comprendre soi-même.\n\nBienvenue sur mon espace.",
                'category_id' => 3,
                'user_id'     => 1,
            ],
        ];

        foreach ($posts as $post) {
            // On ne crée pas si le slug existe déjà
            if (!Post::where('slug', $post['slug'])->exists()) {
                Post::create($post);
            }
        }
    }
}
