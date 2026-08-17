<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Setting;
use App\Models\Skill;
use App\Models\Project;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Default Admin User
        User::create([
            'name' => 'Alex Rivera',
            'email' => 'admin@apvisuals.com',
            'password' => Hash::make('admin123', ['rounds' => 10]),
        ]);

        // 2. Create Default General Settings
        $settings = [
            'hero_title' => 'Merging Pixel with Logic.',
            'hero_subtitle' => 'A multidisciplinary creator specializing in high-end photography, cinematic video editing, and front-end web development. I craft digital experiences that are as functional as they are beautiful.',
            'focus_title' => 'FOCUS: Performance & UI',
            'focus_description' => 'I build highly-optimized, accessible web applications with a focus on immersive user experiences and seamless interactions.',
            'philosophy_title' => 'The Philosophy',
            'philosophy_text' => 'I believe that digital products should feel like second-nature to users. Whether it\'s a 30-second commercial or a complex SaaS platform, my approach remains the same: extreme attention-to-detail, technical rigor, and dynamic storytelling.',
            'philosophy_point1' => 'Precision-engineered code for ultimate performance.',
            'philosophy_point2' => 'Cinematic visual sense for genuine emotion.',
            'philosophy_point3' => 'A seamless bridge between design and development.',
            'philosophy_quote' => 'Quality isn\'t an act, it is a habit.',
            'philosophy_quote_author' => 'Aristotle',
            'social_instagram' => 'https://instagram.com',
            'social_linkedin' => 'https://linkedin.com',
            'social_github' => 'https://github.com',
            'social_vimeo' => 'https://vimeo.com',
            'email' => 'contact@apvisuals.com',
            'profile_image' => 'profile.jpg',
        ];

        foreach ($settings as $key => $value) {
            Setting::create([
                'key' => $key,
                'value' => $value,
            ]);
        }

        // 3. Create Default Skills
        $skills = [
            // Creative Toolkit (Cards)
            ['name' => 'Cinematography', 'category' => 'creative', 'type' => 'card', 'description' => 'Visual storytelling through lenses and framing.', 'order' => 1],
            ['name' => 'Color Grading', 'category' => 'creative', 'type' => 'card', 'description' => 'Narrative color styling in DaVinci Log workflows.', 'order' => 2],
            ['name' => 'Composition', 'category' => 'creative', 'type' => 'card', 'description' => 'Balanced aesthetics across all mediums.', 'order' => 3],
            ['name' => 'Art Direction', 'category' => 'creative', 'type' => 'card', 'description' => 'Unified visual style from concept to delivery.', 'order' => 4],

            // Engineering Toolkit (Badges)
            ['name' => 'HTML5', 'category' => 'engineering', 'type' => 'badge', 'description' => null, 'order' => 5],
            ['name' => 'CSS3', 'category' => 'engineering', 'type' => 'badge', 'description' => null, 'order' => 6],
            ['name' => 'Node.js', 'category' => 'engineering', 'type' => 'badge', 'description' => null, 'order' => 7],
            ['name' => 'TypeScript', 'category' => 'engineering', 'type' => 'badge', 'description' => null, 'order' => 8],
            ['name' => 'Three.js', 'category' => 'engineering', 'type' => 'badge', 'description' => null, 'order' => 9],
            ['name' => 'Next.js', 'category' => 'engineering', 'type' => 'badge', 'description' => null, 'order' => 10],
            ['name' => 'Laravel', 'category' => 'engineering', 'type' => 'badge', 'description' => null, 'order' => 11],
        ];

        foreach ($skills as $skill) {
            Skill::create($skill);
        }

        // 4. Create Default Projects
        $projects = [
            [
                'title' => 'Deep Sea Project',
                'description' => 'A cinematic explore into underwater habitats and marine typography. Shot on RED V-Raptor with custom marine enclosures.',
                'category' => 'Cinematography',
                'tags' => 'Video, Cinematography, Color Grading',
                'type' => 'image',
                'asset_path' => 'project1.jpg',
                'project_url' => 'https://vimeo.com',
                'order' => 1
            ],
            [
                'title' => 'Neon Portraiture',
                'description' => 'Exploring cyber-punk aesthetics using high-end studio lighting and vibrant purple color spaces.',
                'category' => 'Photography',
                'tags' => 'Photography, Lighting, Portrait',
                'type' => 'image',
                'asset_path' => 'project2.jpg',
                'project_url' => '#',
                'order' => 2
            ],
            [
                'title' => 'Kinetic Light Trail',
                'description' => 'An abstract exploration of time-lapse photography and light movement in modern metropolis landscapes.',
                'category' => 'Color Grading',
                'tags' => 'DaVinci Resolve, Motion Design',
                'type' => 'image',
                'asset_path' => 'project3.jpg',
                'project_url' => 'https://vimeo.com',
                'order' => 3
            ],
            [
                'title' => 'Minimalist Dashboard UI',
                'description' => 'A premium admin dashboard mock designed for developers. Clean interfaces with high contrast glassmorphism structures.',
                'category' => 'Engineering',
                'tags' => 'UI/UX, Frontend, CSS Variables',
                'type' => 'image',
                'asset_path' => 'project4.jpg',
                'project_url' => '#',
                'order' => 4
            ]
        ];

        foreach ($projects as $project) {
            Project::create($project);
        }
    }
}
