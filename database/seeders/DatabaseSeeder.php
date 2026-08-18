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
            'name' => 'Aditya Putra',
            'email' => 'admin@apvisuals.com',
            'password' => Hash::make('admin123', ['rounds' => 10]),
        ]);

        // 2. Create Default General Settings
        $settings = [
            'hero_title' => 'Merging Pixel with Logic.',
            'hero_subtitle' => 'A multidisciplinary creator specializing in high-end photography, cinematic video editing, and front-end web development. I craft digital experiences that are as functional as they are beautiful.',
            'about_name' => 'Aditya Putra',
            'about_role' => 'Cinematographer · Visual Director · Web Developer',
            'about_location' => 'Pontianak, Kalimantan Barat',
            'about_experience' => '9',
            'about_projects' => '50',
            'about_clients' => '20',
            'focus_title' => 'FOCUS: Performance & UI',
            'focus_description' => 'I build highly-optimized, accessible web applications with a focus on immersive user experiences and seamless interactions.',
            'philosophy_title' => 'The Philosophy',
            'philosophy_text' => 'I\'m Aditya Putra, a creative developer and visual creator from Indonesia. I combine technology and visual creativity to create digital experiences and visual content that are modern, engaging, and meaningful. My work spans web development, photography, videography, photo editing, and video editing. I enjoy turning ideas into polished digital products and visual content, from building responsive websites and applications to creating and editing photos and videos with attention to detail. I believe great creative work comes from the balance of technology, visual storytelling, and precision. Whether I\'m writing code, capturing a moment, or editing a visual, I always aim to create something that looks good, works well, and leaves an impression.',
            'philosophy_point1_title' => 'Visual Craft',
            'philosophy_point1' => 'Photography, videography, and professional photo & video editing focused on composition, detail, and storytelling.',
            'philosophy_point2_title' => 'Web Development',
            'philosophy_point2' => 'Building modern, responsive, and functional websites and applications with clean design and intuitive user experiences.',
            'philosophy_point3_title' => 'Seamless Bridge',
            'philosophy_point3' => 'A seamless bridge between design and development.',
            'philosophy_quote' => 'Quality isn\'t an act, it is a habit.',
            'philosophy_quote_author' => 'Aristotle',
            'social_instagram' => 'https://www.instagram.com/apvisualsgallery',
            'social_tiktok' => 'https://www.tiktok.com/@ap.visuals_',
            'social_linkedin' => 'https://www.linkedin.com/in/aditya-putra-2b9989325',
            'social_github' => 'https://github.com/adityaputr23',
            'email' => 'speedvisuals03@gmail.com',
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
