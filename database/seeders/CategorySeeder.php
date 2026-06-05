<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Account Security',              'slug' => 'account-security',      'icon' => 'shield-check', 'description' => 'Protect your online accounts from compromise — passwords, MFA, sessions.'],
            ['name' => 'Malware & Ransomware',          'slug' => 'malware-ransomware',    'icon' => 'bug',          'description' => 'Detect, contain, and recover from malicious software including ransomware.'],
            ['name' => 'Phishing & Social Engineering', 'slug' => 'phishing',              'icon' => 'fish',         'description' => 'Recognize and respond to deceptive emails, calls, and messages.'],
            ['name' => 'Network Security',              'slug' => 'network-security',      'icon' => 'network',      'description' => 'Secure your home or office network and the devices connected to it.'],
            ['name' => 'Device Security',               'slug' => 'device-security',       'icon' => 'smartphone',   'description' => 'Lock down phones, laptops, and other devices against loss or theft.'],
            ['name' => 'Data Breach',                   'slug' => 'data-breach',           'icon' => 'database',     'description' => 'Find out if your data has been exposed and what to do next.'],
            ['name' => 'Preventive Advice',             'slug' => 'preventive-advice',     'icon' => 'lightbulb',    'description' => 'Best practices to reduce your overall exposure before something happens.'],
        ];

        foreach ($categories as $c) {
            Category::updateOrCreate(['slug' => $c['slug']], $c);
        }
    }
}
