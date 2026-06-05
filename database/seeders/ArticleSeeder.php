<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        $articles = [
            // Account Security
            ['account-security', 'How to create a strong, unique password',
                'password,passphrase,credentials,login',
                <<<'MD'
A strong password is your first line of defense. Attackers routinely test millions of common passwords against leaked email lists, so reusing the same one across multiple sites greatly increases your risk.

A good password is **long, unique, and unpredictable**. Modern guidance favors a passphrase of four or more unrelated words over a short string of special characters.

1. Use at least 14 characters — longer is exponentially harder to crack.
2. Never reuse a password across more than one account.
3. Store them in a reputable password manager so length stops being a memory problem.
4. Replace immediately any password reused on a site you suspect was breached.

## When to ask for expert help

If you suspect an attacker is actively using your password — for example, a service tells you about a login from a country you have never visited — open a ticket so we can walk you through containment, password rotation order, and session revocation.
MD
            ],
            ['account-security', 'Turn on multi-factor authentication everywhere',
                'mfa,2fa,authenticator,otp',
                <<<'MD'
Multi-factor authentication (MFA) requires a second proof of identity on top of your password. Even a fully leaked password becomes much less useful to an attacker if they do not also control your phone or hardware key.

Prefer **app-based** codes (Google Authenticator, 1Password, Authy) or **hardware keys** (YubiKey) over SMS. SMS is better than nothing but is vulnerable to SIM-swap attacks.

1. Enable MFA on your email account first — it is the recovery path for everything else.
2. Move on to your password manager, then banking and work accounts.
3. Save backup codes somewhere offline (printed, in a safe) in case you lose your device.

## When to ask for expert help

If you have lost both your phone and your backup codes, do not panic. Open a ticket — we can help you sequence the account recovery steps without leaving accounts orphaned.
MD
            ],
            ['account-security', 'What to do right after an account is compromised',
                'compromise,account takeover,recovery,response',
                <<<'MD'
If you believe someone else has accessed one of your accounts, speed matters. The attacker is likely to extract value (email forwarding, password resets, OAuth grants) within minutes.

1. From a different device, change the password to a fresh, unique one.
2. Sign out all active sessions in the account security settings.
3. Review and remove suspicious mail filters, forwarding rules, recovery emails, and connected apps.
4. Re-enable MFA if it was disabled, and rotate any password reused elsewhere.

## When to ask for expert help

If the compromised account is your primary email or your password manager, open a high-severity ticket. We can guide the recovery order so the attacker does not regain access through the recovery chain you just used.
MD
            ],

            // Malware & Ransomware
            ['malware-ransomware', 'You think your computer has malware — first 30 minutes',
                'malware,virus,trojan,infection',
                <<<'MD'
A device acting strangely — pop-ups, fans spinning hard for no reason, programs you did not install — may be infected. The goal of the first 30 minutes is to **stop the bleeding** without destroying evidence.

1. Disconnect the device from Wi-Fi and unplug any Ethernet cable.
2. Do **not** shut down — many forensic artifacts only exist in memory.
3. Stop using the device for sensitive logins or banking from anywhere.
4. From a *different*, clean device, change passwords for any account you used recently.

## When to ask for expert help

If the device belongs to your employer, or you handle financial or health data on it, open a ticket immediately rather than running a cleanup yourself. Reformatting too soon destroys evidence and may breach reporting obligations.
MD
            ],
            ['malware-ransomware', 'Ransomware: what to do (and what NOT to do)',
                'ransomware,encryption,extortion,backup',
                <<<'MD'
Ransomware encrypts your files and demands payment for a decryption key. Paying does **not** guarantee recovery — and it funds further attacks.

1. Isolate the affected device from every network it touches.
2. Photograph the ransom note (do not just close it).
3. Identify the strain via ID Ransomware or VirusTotal before doing anything else.
4. Restore from an offline backup if available. If not, preserve the encrypted disk in case a free decryptor is released later.

## When to ask for expert help

Ransomware should always be treated as a high-severity ticket. Open one before paying, before wiping, before talking to the press, and before contacting law enforcement — the order matters.
MD
            ],

            // Phishing & Social Engineering
            ['phishing', 'Recognizing a phishing email in under 10 seconds',
                'phishing,email,scam,impersonation',
                <<<'MD'
Phishing emails try to trick you into clicking a link, opening an attachment, or replying with sensitive data. They are increasingly convincing — even copying real corporate templates.

1. Check the sender address character-by-character (not just the display name).
2. Hover over every link before clicking — does the URL match the brand?
3. Be suspicious of urgency, fear, or unexpected money.
4. When in doubt, reach the sender through a channel you already trust.

## When to ask for expert help

If you have already clicked a link or entered credentials, open a ticket immediately and treat any affected accounts as compromised. The sooner you do this, the smaller the blast radius.
MD
            ],
            ['phishing', 'Smishing and vishing: phishing over SMS and phone',
                'smishing,vishing,sms scam,phone scam,social engineering',
                <<<'MD'
Not all phishing arrives by email. **Smishing** (SMS) and **vishing** (voice) attacks bypass corporate email filters and rely on time pressure to short-circuit your judgement.

1. Never call back a number from an unsolicited SMS — look the official one up yourself.
2. Banks, tax agencies, and shipping companies will never ask for full passwords or one-time codes.
3. If the caller claims to be from your IT department, hang up and call the IT helpdesk directly.

## When to ask for expert help

If you read a one-time code aloud, your account is compromised — open a ticket and we will walk you through revoking sessions and resetting credentials in the right order.
MD
            ],

            // Network Security
            ['network-security', 'Securing your home Wi-Fi router',
                'router,wifi,wpa3,home network',
                <<<'MD'
Your home router is the front door to every device behind it. Default settings on consumer routers are convenient — and often dangerously open.

1. Change the default admin password. Most routers still ship with `admin / admin`.
2. Set Wi-Fi security to **WPA3** (or WPA2 if WPA3 is unavailable). Never use WEP.
3. Disable remote management unless you specifically need it.
4. Keep firmware up to date — many routers can auto-update if you allow it.

## When to ask for expert help

If you notice devices on your network that you do not recognize, or your bandwidth is being used at strange hours, open a ticket. We can help you isolate the rogue device before deciding whether to factory-reset.
MD
            ],
            ['network-security', 'Using public Wi-Fi without getting burned',
                'public wifi,vpn,hotspot,coffee shop',
                <<<'MD'
Public Wi-Fi is convenient but inherently low-trust: you do not control the access point or the other devices on the network.

1. Assume any unencrypted traffic on the network is being read.
2. Prefer your phone's hotspot for anything financial or work-sensitive.
3. If you must use public Wi-Fi, use a reputable VPN — and remember that the VPN provider itself can see your traffic.
4. Make sure every site you visit shows the lock icon (HTTPS).

## When to ask for expert help

If you logged into a sensitive account over a suspicious public network and now see unexpected activity, treat the account as compromised and open a ticket.
MD
            ],

            // Device Security
            ['device-security', 'Lock-screen settings that actually matter',
                'lock screen,pin,passcode,biometrics',
                <<<'MD'
The lock screen is the only thing between an opportunistic thief and your entire digital life. A weak or absent lock screen makes every other defense moot.

1. Use a 6+ digit PIN, alphanumeric passcode, or biometric unlock — not a 4-digit PIN.
2. Set the auto-lock to one minute or less.
3. Enable "Erase data after 10 failed attempts" on phones that support it.
4. Make sure notifications hide their content on the lock screen.

## When to ask for expert help

If your phone is lost and you do not remember whether you had Find My / Find My Device enabled, open a ticket fast. We can help you assess what is at risk and what to revoke first.
MD
            ],
            ['device-security', 'What to do if your laptop or phone is stolen',
                'theft,lost device,wipe,remote',
                <<<'MD'
A stolen device is a credentials and data leak in progress. The right response in the first hour can reduce the damage to nearly zero.

1. Mark the device as lost in Find My Device / Find My iPhone — this triggers tracking and disables Apple/Google Pay.
2. Change the password to your primary email immediately.
3. Sign out of all sessions in any account you used on the device.
4. File a report with your local police if the device contained sensitive personal or work data.

## When to ask for expert help

Open a high-severity ticket if work email or banking apps were signed in on the lost device — we will help you sequence the password resets and revoke OAuth grants in the right order.
MD
            ],

            // Data Breach
            ['data-breach', 'You got a breach notification — now what?',
                'breach,leak,hibp,exposure',
                <<<'MD'
Breach notifications are uncomfortable but useful: they give you a chance to act before attackers do.

1. Identify exactly **what** was exposed — emails, passwords, addresses, payment data, social security number — and treat each category differently.
2. If passwords were exposed, change the password everywhere that one was reused.
3. If financial data was exposed, freeze your credit and review statements for at least 90 days.
4. Save the notification — you may need it later for fraud claims.

## When to ask for expert help

Open a ticket if you are unsure what to rotate first. Order matters: rotating your password manager master password before recovering your email can lock you out.
MD
            ],
            ['data-breach', 'Identity theft: warning signs and first response',
                'identity theft,fraud,credit freeze,ssn',
                <<<'MD'
Identity theft typically shows up as small, unexpected events: a credit card application you did not make, a tax filing rejection, a debt collector calling about a loan you never took out.

1. Place a fraud alert (and ideally a freeze) at all three credit bureaus.
2. Pull your credit reports and review every line.
3. File a report at IdentityTheft.gov (US) or your country's equivalent.
4. Change passwords on financial accounts and turn on transaction alerts.

## When to ask for expert help

If you suspect your identity was used to open new accounts, open a ticket — the legal and reporting steps differ by jurisdiction and we can help you avoid missing a deadline.
MD
            ],

            // Preventive Advice
            ['preventive-advice', 'A 20-minute monthly security checkup',
                'checkup,maintenance,hygiene,routine',
                <<<'MD'
The biggest security wins are small habits repeated. Set a recurring 20-minute calendar event once a month for these.

1. Run the password manager's "compromised passwords" report and fix what it flags.
2. Skim recent login activity on your email, bank, and any work tools.
3. Check that every device has applied OS and browser updates.
4. Remove apps you no longer use and revoke OAuth grants you do not recognize.

## When to ask for expert help

If your monthly check turns up activity you cannot explain, open a ticket rather than dismissing it — we can help you investigate without burning evidence.
MD
            ],
            ['preventive-advice', 'Backing up the things you cannot afford to lose',
                'backup,3-2-1,cloud,recovery',
                <<<'MD'
The classic **3-2-1 rule** still holds: three copies of your data, on two different media, with one copy offsite. Ransomware, fire, and accidental deletion all defeat single-copy strategies.

1. Pick one **primary** cloud backup with versioning (so ransomware does not overwrite all your good versions).
2. Keep a second **local** copy on an external drive that is only plugged in during backup.
3. Test a restore at least twice a year — backups that have never been tested are not backups.

## When to ask for expert help

Open a ticket before restoring from a backup after an incident. Restoring too fast can re-infect the clean machine, undoing all your containment work.
MD
            ],
        ];

        foreach ($articles as [$categorySlug, $title, $keywords, $content]) {
            $category = Category::where('slug', $categorySlug)->first();
            if (!$category) {
                continue;
            }

            Article::updateOrCreate(
                ['slug' => Str::slug($title)],
                [
                    'title' => $title,
                    'slug' => Str::slug($title),
                    'content' => $content,
                    'category_id' => $category->id,
                    'keywords' => $keywords,
                ]
            );
        }
    }
}
