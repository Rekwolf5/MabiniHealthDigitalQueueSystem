<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Project::create([
            'title' => 'Alaminos City Tourist Webpage',
            'description' => 'A comprehensive website showcasing tourist attractions, accommodations, and activities in Alaminos City. Built with responsive design principles to ensure optimal viewing across all devices.',
            'image' => 'images/alaminos-project.jpg',
            'technologies' => 'HTML, CSS, JavaScript, Bootstrap',
            'url' => '#',
        ]);

        Project::create([
            'title' => 'Inventory Management System',
            'description' => 'A simple yet effective inventory management system designed for small businesses. Features include stock tracking, low inventory alerts, and basic reporting capabilities.',
            'image' => 'images/inventory-project.jpg',
            'technologies' => 'PHP, MySQL, Bootstrap, jQuery',
            'url' => '#',
        ]);

        Project::create([
            'title' => 'Weather Forecast Application',
            'description' => 'A web application that provides real-time weather information and forecasts for locations worldwide. Integrates with a third-party weather API to fetch accurate data.',
            'image' => 'images/weather-project.jpg',
            'technologies' => 'JavaScript, React, API Integration',
            'url' => '#',
        ]);
    }
}
