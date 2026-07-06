<?php
/**
 * functions.php — presentation helpers for job listings.
 *
 * Every job is given a "category" derived from its title, which maps to a
 * colour + Font Awesome icon. This lets each job card show a rich, coloured
 * banner and a company "logo" avatar without needing any uploaded images or
 * external image services — everything renders from CSS + Font Awesome.
 */

if (!function_exists('job_category')) {
    /**
     * Guess a job's category from its title and return its icon + gradient.
     */
    function job_category($title)
    {
        $t = strtolower($title);

        $categories = [
            'Design'      => ['keys' => ['ui', 'ux', 'design', 'graphic', 'creative'],                        'icon' => 'fa-pen-nib',    'grad' => ['#8b5cf6', '#6d28d9']],
            'Marketing'   => ['keys' => ['market', 'seo', 'content', 'social', 'growth', 'brand'],            'icon' => 'fa-bullhorn',   'grad' => ['#f59e0b', '#d97706']],
            'Finance'     => ['keys' => ['financ', 'account', 'analyst', 'bank', 'audit', 'invest'],          'icon' => 'fa-chart-line', 'grad' => ['#059669', '#047857']],
            'Data'        => ['keys' => ['data', 'machine learning', 'scientist', 'analytics'],               'icon' => 'fa-database',   'grad' => ['#0891b2', '#0e7490']],
            'DevOps'      => ['keys' => ['devops', 'cloud', 'sre', 'infrastructure', 'network'],              'icon' => 'fa-server',     'grad' => ['#475569', '#1e293b']],
            'Support'     => ['keys' => ['support', 'customer', 'service', 'help', 'success'],                'icon' => 'fa-headset',    'grad' => ['#0ea5e9', '#0284c7']],
            'Sales'       => ['keys' => ['sales', 'business development', 'account executive'],               'icon' => 'fa-handshake',  'grad' => ['#e11d48', '#be123c']],
            'Engineering' => ['keys' => ['develop', 'engineer', 'programmer', 'php', 'react', 'front', 'back', 'software', 'web', 'mobile'], 'icon' => 'fa-code', 'grad' => ['#4f46e5', '#4338ca']],
        ];

        foreach ($categories as $name => $cat) {
            foreach ($cat['keys'] as $k) {
                if (strpos($t, $k) !== false) {
                    $cat['name'] = $name;
                    return $cat;
                }
            }
        }

        return ['name' => 'General', 'icon' => 'fa-briefcase', 'grad' => ['#4f46e5', '#4338ca']];
    }
}

if (!function_exists('company_initials')) {
    /**
     * Build up to two initials from a company name for the logo avatar.
     */
    function company_initials($company)
    {
        $words = preg_split('/\s+/', trim($company));
        $initials = '';
        foreach ($words as $w) {
            if ($w !== '' && ctype_alnum($w[0])) {
                $initials .= strtoupper($w[0]);
            }
            if (strlen($initials) >= 2) {
                break;
            }
        }
        return $initials !== '' ? $initials : '?';
    }
}

if (!function_exists('job_banner_html')) {
    /**
     * Coloured banner + company-initial logo shown at the top of a job card.
     */
    function job_banner_html($title, $company)
    {
        $cat  = job_category($title);
        $grad = "linear-gradient(135deg, {$cat['grad'][0]} 0%, {$cat['grad'][1]} 100%)";
        $icon = $cat['icon'];
        $name = htmlspecialchars($cat['name']);
        $ini  = htmlspecialchars(company_initials($company));

        return '<div class="job-banner" style="background:' . $grad . '">'
             . '<div class="job-logo">' . $ini . '</div>'
             . '<span class="job-cat-tag">' . $name . '</span>'
             . '<i class="fas ' . $icon . ' job-watermark"></i>'
             . '</div>';
    }
}
