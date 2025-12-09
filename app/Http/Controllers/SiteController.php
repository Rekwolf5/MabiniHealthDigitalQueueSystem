<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SiteController extends Controller
{
    public function home()
    {
        return view('pages.home');
    }

    public function about()
    {
        return view('pages.about');
    }

    public function skills()
    {
        return view('pages.skills');
    }

    public function portfolio()
    {
        // Static project data instead of database
        $projects = [
        [
             'title' => 'Alaminos City Tourist Webpage',
             'description' => 'An informative and visually engaging website highlighting tourist attractions, accommodations, local events, and activities in Alaminos City. Designed with a responsive layout for seamless access across all devices.',
                'image' => 'images/alaminos.png',
         'technologies' => 'HTML, CSS, JavaScript, Bootstrap',
             'url' => '#',
],

           [
            'title' => 'Appointment and Queuing Management System',
             'description' => 'A web-based system that streamlines appointment scheduling and queue management for service-based businesses. Features include real-time queue updates, appointment booking, and notifications.',
            'image' => 'images/appointment.png',
             'technologies' => 'PHP, MySQL, Bootstrap, jQuery, javascript, laravel, firebase',
                'url' => '#',
],

           [
                'title' => 'Online Sourcing Hardware',
                'description' => 'A web platform designed for sourcing hardware components online. Users can browse, compare, and request quotations for various hardware products from multiple suppliers.',
                 'image' => 'images/hardware.png',
                 'technologies' => 'Html, Css, JavaScript, React, API Integration',
                 'url' => '#',
],

        ];
        
        return view('pages.portfolio', compact('projects'));
    }

    public function contact()
    {
        return view('pages.contact');
    }

    public function submitContact(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string',
        ]);

        // In a real application, you would process the form data here
        // For example, send an email or store in database

        return redirect()->route('contact')->with('success', 'Your message has been received!');
    }
}
