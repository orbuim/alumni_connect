
<?php

return [
    'profiles' => [
        'title' => 'Alumni-profielen',
        'fields' => [
            'education' => 'Opleiding',
            'graduation_year' => 'Afstudeerjaar',
            'job' => 'Beroep',
            'skills' => 'Vaardigheden'
        ],
        'role' => 'alumnus'
    ],

    'opportunities' => [
        'title' => 'Stage- en bedrijfskansen',
        'fields' => [
            'title' => 'Titel',
            'company' => 'Bedrijf',
            'description' => 'Omschrijving'
        ],
        'role' => 'bedrijf'
    ],

    'events' => [
        'title' => 'Evenementen',
        'fields' => [
            'title' => 'Titel',
            'event_date' => 'Datum (JJJJ-MM-DD)',
            'location' => 'Locatie',
            'description' => 'Omschrijving'
        ],
        'role' => 'organisator'
    ],

    'registrations' => [
        'title' => 'Aanmeldingen',
        'fields' => [
            'event_id' => 'Evenement-ID',
            'status' => 'Status (aangemeld/geannuleerd)'
        ],
        'role' => 'all'
    ],

    'questions' => [
        'title' => 'Vragen',
        'fields' => [
            'title' => 'Titel',
            'body' => 'Vraag'
        ],
        'role' => 'all'
    ],

    'answers' => [
        'title' => 'Antwoorden',
        'fields' => [
            'question_id' => 'Vraag-ID',
            'body' => 'Antwoord'
        ],
        'role' => 'all'
    ],

    'news' => [
        'title' => 'Nieuwsberichten',
        'fields' => [
            'title' => 'Titel',
            'body' => 'Inhoud',
            'published' => 'Gepubliceerd (0/1)'
        ],
        'role' => 'beheerder'
    ],

    'matches' => [
        'title' => 'Mentor-matches',
        'fields' => [
            'student_id' => 'Student-ID',
            'mentor_id' => 'Mentor-ID'
        ],
        'role' => 'beheerder'
    ],

    'feedback' => [
        'title' => 'Portfoliofeedback',
        'fields' => [
            'student_id' => 'Student-ID',
            'portfolio_url' => 'Portfolio URL',
            'body' => 'Feedback'
        ],
        'role' => 'mentor'
    ]
];
