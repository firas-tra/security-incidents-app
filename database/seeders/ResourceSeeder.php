<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Resource;
use Illuminate\Database\Seeder;

class ResourceSeeder extends Seeder
{
    public function run(): void
    {
        $resources = [
            ['account-security',   'Have I Been Pwned',           'Check whether your email or phone has appeared in a known data breach.',          'https://haveibeenpwned.com',          'shield-alert'],
            ['account-security',   'Firefox Monitor',             'Mozilla\'s free breach-monitoring service, with optional ongoing alerts.',        'https://monitor.firefox.com',         'alert-circle'],

            ['malware-ransomware', 'VirusTotal',                  'Scan files, URLs, and hashes against 70+ antivirus engines and sandboxes.',       'https://virustotal.com',              'scan'],
            ['malware-ransomware', 'Malwarebytes',                'Free on-demand malware scanner trusted for second-opinion cleanups.',             'https://www.malwarebytes.com',        'shield'],
            ['malware-ransomware', 'ID Ransomware',               'Identify which ransomware strain encrypted your files from a sample or note.',    'https://id-ransomware.malwarehunterteam.com', 'key-round'],

            ['phishing',           'PhishTank',                   'Community-driven database of confirmed phishing URLs you can report and look up.','https://phishtank.org',               'fish'],
            ['phishing',           'Google Safe Browsing',        'Check whether a URL is on Google\'s list of unsafe (phishing or malware) sites.',  'https://transparencyreport.google.com/safe-browsing/search', 'search-check'],

            ['network-security',   'Shodan',                      'Search engine for internet-exposed devices — useful to see what you\'re leaking.','https://shodan.io',                    'globe'],
            ['network-security',   'urlscan.io',                  'Sandbox a URL and see exactly what it does in a browser before you visit it.',    'https://urlscan.io',                  'link'],

            ['device-security',    'Prey Project',                'Track, lock, and wipe lost or stolen laptops and phones across platforms.',       'https://preyproject.com',             'locate'],
            ['device-security',    'Find My Device (Google)',     'Google\'s remote locate / ring / wipe tool for signed-in Android devices.',       'https://www.google.com/android/find', 'smartphone'],

            ['data-breach',        'DeHashed',                    'Search across leaked credential corpuses for exposed data tied to your identity.','https://www.dehashed.com',            'database'],
            ['data-breach',        'IntelX',                      'Search engine for leaked data, paste sites, and darkweb forums.',                  'https://intelx.io',                   'database-zap'],

            ['preventive-advice',  'NIST Cybersecurity Framework','U.S. government\'s reference framework for managing cybersecurity risk.',         'https://www.nist.gov/cyberframework', 'book-open'],
            ['preventive-advice',  'KrebsOnSecurity',             'In-depth investigative reporting on cybercrime and security incidents.',          'https://krebsonsecurity.com',         'newspaper'],
        ];

        foreach ($resources as [$categorySlug, $name, $description, $url, $icon]) {
            $category = Category::where('slug', $categorySlug)->first();
            if (!$category) {
                continue;
            }

            Resource::updateOrCreate(
                ['url' => $url],
                [
                    'name' => $name,
                    'description' => $description,
                    'url' => $url,
                    'category_id' => $category->id,
                    'icon' => $icon,
                ]
            );
        }
    }
}
