
<?php
require 'includes/init.php';

$cards = [
    'profiles' => [
        'icon' => 'bi-person-fill',
        'color' => 'violet',
        'desc' => 'Bekijk en ontdek alumni en hun achtergrond, ervaring en vaardigheden.'
    ],
    'opportunities' => [
        'icon' => 'bi-briefcase-fill',
        'color' => 'green',
        'desc' => 'Vind de nieuwste stages, vacatures en bedrijfskansen bij topbedrijven.'
    ],
    'events' => [
        'icon' => 'bi-calendar-event-fill',
        'color' => 'orange',
        'desc' => 'Bekijk en meld je aan voor interessante evenementen en netwerkbijeenkomsten.'
    ],
    'registrations' => [
        'icon' => 'bi-people-fill',
        'color' => 'red',
        'desc' => 'Beheer je aanmeldingen voor evenementen en activiteiten.'
    ],
    'questions' => [
        'icon' => 'bi-question-circle-fill',
        'color' => 'blue',
        'desc' => 'Stel vragen en krijg hulp van andere gebruikers, alumni en docenten.'
    ],
    'answers' => [
        'icon' => 'bi-chat-left-text-fill',
        'color' => 'purple',
        'desc' => 'Help anderen door je kennis en ervaring te delen.'
    ],
    'news' => [
        'icon' => 'bi-newspaper',
        'color' => 'teal',
        'desc' => 'Blijf op de hoogte van het laatste nieuws, updates en aankondigingen.'
    ],
    'matches' => [
        'icon' => 'bi-person-hearts',
        'color' => 'pink',
        'desc' => 'Koppel aan een mentor of student en groei samen.'
    ],
    'feedback' => [
        'icon' => 'bi-star-fill',
        'color' => 'indigo',
        'desc' => 'Ontvang en geef feedback op portfolios om je verder te ontwikkelen.'
    ]
];

$counts = [];

foreach (['events', 'opportunities', 'answers', 'feedback'] as $table) {
    try {
        $counts[$table] = (int)$pdo
            ->query("SELECT COUNT(*) FROM `$table`")
            ->fetchColumn();
    } catch (PDOException $e) {
        $counts[$table] = 0;
    }
}

$recent = [];

$activityTypes = [
    'opportunities' => 'Stagekans toegevoegd',
    'events' => 'Evenement toegevoegd',
    'questions' => 'Vraag geplaatst',
    'feedback' => 'Portfoliofeedback toegevoegd'
];

foreach ($activityTypes as $table => $label) {
    try {
        $stmt = $pdo->query(
            "SELECT id, created_at FROM `$table`
             ORDER BY created_at DESC LIMIT 2"
        );

        foreach ($stmt->fetchAll() as $row) {
            $recent[] = [
                'entity' => $table,
                'label' => $label,
                'time' => $row['created_at']
            ];
        }
    } catch (PDOException $e) {
        // Geen activiteiten beschikbaar
    }
}

usort(
    $recent,
    fn($a, $b) => strcmp($b['time'], $a['time'])
);

$recent = array_slice($recent, 0, 4);

$name = me()['name'] ?? 'Gast';
$role = me()['role'] ?? 'Bezoeker';
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard | Alumni Connect</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        rel="stylesheet"
    >

    <link rel="stylesheet" href="/alumni_connect/assets/style.css?v=2">
</head>

<body class="dashboard-body">

<!-- TOP NAVBAR -->
<header class="topbar">

    <a class="brand" href="index.php">
        <i class="bi bi-mortarboard-fill"></i>
        <span>Alumni <b>Connect</b></span>
    </a>

    <form class="top-search" id="searchForm" role="search">
        <i class="bi bi-search"></i>

        <input
            id="dashboardSearch"
            type="search"
            placeholder="Zoek naar alumni, bedrijven, evenementen..."
            aria-label="Zoek onderdelen"
        >
    </form>

    <div class="top-user">
        <i class="bi bi-bell"></i>

        <span class="top-separator"></span>

        <span class="avatar">
            <i class="bi bi-person-fill"></i>
        </span>

        <span class="user-name">
            <?= h($name) ?>
            <small><?= h(ucfirst($role)) ?></small>
        </span>

        <i class="bi bi-chevron-down chevron"></i>
    </div>

</header>

<!-- SIDEBAR -->
<aside class="sidebar">

    <nav class="side-nav">

        <a class="active" href="index.php">
            <i class="bi bi-house-door"></i>
            Dashboard
        </a>

        <?php foreach ($cards as $key => $card): ?>

            <a href="crud.php?entity=<?= h($key) ?>">
                <i class="bi <?= h($card['icon']) ?>"></i>
                <?= h($entities[$key]['title']) ?>
            </a>

        <?php endforeach; ?>

    </nav>

    <div class="side-bottom">

        <?php if (me()): ?>

            <a href="account.php">
                <i class="bi bi-person"></i>
                Mijn account
            </a>

            <a href="logout.php">
                <i class="bi bi-box-arrow-right"></i>
                Uitloggen
            </a>

        <?php else: ?>

            <a href="login.php">
                <i class="bi bi-box-arrow-in-right"></i>
                Inloggen
            </a>

            <a href="register.php">
                <i class="bi bi-person-plus"></i>
                Registreren
            </a>

        <?php endif; ?>

    </div>

</aside>

<!-- MAIN CONTENT -->
<main class="dashboard-main">

    <!-- WELCOME BANNER -->
    <section class="welcome-banner">

        <div class="welcome-content">

            <i class="bi bi-mortarboard-fill welcome-icon"></i>

            <div>
                <h1>Welkom bij Alumni Connect!</h1>

                <p>
                    Dé plek waar studenten, alumni, docenten en bedrijven samenkomen.
                    <br>
                    Bouw aan je toekomst, deel kennis en ontdek nieuwe kansen.
                </p>
            </div>

        </div>

    </section>

    <!-- FEATURE CARDS -->
    <section class="feature-grid" aria-label="Onderdelen">

        <?php foreach ($cards as $key => $card): ?>

            <article
                class="feature-card"
                data-search="<?= h(strtolower(
                    $entities[$key]['title'] . ' ' . $card['desc']
                )) ?>"
            >

                <span class="feature-icon <?= h($card['color']) ?>">
                    <i class="bi <?= h($card['icon']) ?>"></i>
                </span>

                <div class="feature-body">

                    <h2><?= h($entities[$key]['title']) ?></h2>

                    <p><?= h($card['desc']) ?></p>

                    <a
                        class="feature-button <?= h($card['color']) ?>"
                        href="crud.php?entity=<?= h($key) ?>"
                    >
                        Bekijk
                    </a>

                </div>

                <a
                    class="card-arrow"
                    href="crud.php?entity=<?= h($key) ?>"
                    aria-label="Open <?= h($entities[$key]['title']) ?>"
                >
                    <i class="bi bi-chevron-right"></i>
                </a>

            </article>

        <?php endforeach; ?>

    </section>

    <p id="noResults" class="no-results" hidden>
        Geen onderdelen gevonden.
    </p>

    <!-- BOTTOM PANELS -->
    <section class="bottom-grid">

        <!-- STATISTICS -->
        <div class="info-panel">

            <h2>Jouw voortgang</h2>

            <p>Ontdek wat je kunt doen en waar je staat.</p>

            <div class="stat-grid">

                <?php
                $stats = [
                    ['events', 'bi-calendar-event-fill', 'violet', 'Evenementen'],
                    ['opportunities', 'bi-briefcase-fill', 'green', 'Kansen'],
                    ['answers', 'bi-chat-left-text-fill', 'blue', 'Antwoorden'],
                    ['feedback', 'bi-star-fill', 'orange', 'Feedback']
                ];
                ?>

                <?php foreach ($stats as $stat): ?>

                    <div class="stat">

                        <span class="stat-icon <?= h($stat[2]) ?>">
                            <i class="bi <?= h($stat[1]) ?>"></i>
                        </span>

                        <strong><?= $counts[$stat[0]] ?></strong>

                        <small><?= h($stat[3]) ?></small>

                    </div>

                <?php endforeach; ?>

            </div>

        </div>

        <!-- RECENT ACTIVITY -->
        <div class="info-panel">

            <h2>
                <i class="bi bi-clock-history"></i>
                Recente activiteiten
            </h2>

            <div class="activity-list">

                <?php if (!$recent): ?>
                    <p>Nog geen activiteiten beschikbaar.</p>
                <?php endif; ?>

                <?php foreach ($recent as $activity): ?>

                    <?php $card = $cards[$activity['entity']]; ?>

                    <a
                        class="activity"
                        href="crud.php?entity=<?= h($activity['entity']) ?>"
                    >

                        <span class="activity-icon <?= h($card['color']) ?>">
                            <i class="bi <?= h($card['icon']) ?>"></i>
                        </span>

                        <span>
                            <?= h($activity['label']) ?>

                            <small>
                                <?= h(date(
                                    'd-m-Y H:i',
                                    strtotime($activity['time'])
                                )) ?>
                            </small>
                        </span>

                    </a>

                <?php endforeach; ?>

            </div>

        </div>

        <!-- FUTURE PANEL -->
        <div class="info-panel future-panel">

            <h2>
                <i class="bi bi-mountains"></i>
                Jouw toekomst begint hier
            </h2>

            <p>Leren, groeien, verbinden</p>

            <blockquote>
                "Succes komt niet van wat je weet,
                maar van wat je doet met wat je weet."
            </blockquote>

            <a
                href="crud.php?entity=opportunities"
                class="discover"
            >
                Ontdek alle mogelijkheden
                <i class="bi bi-arrow-right"></i>
            </a>

        </div>

    </section>

</main>

<!-- SEARCH JAVASCRIPT -->
<script>
const search = document.getElementById('dashboardSearch');
const form = document.getElementById('searchForm');

function filterCards() {
    const query = search.value.trim().toLocaleLowerCase('nl');
    let visible = 0;

    document.querySelectorAll('.feature-card').forEach(card => {
        const show = card.dataset.search.includes(query);

        card.hidden = !show;

        if (show) {
            visible++;
        }
    });

    document.getElementById('noResults').hidden = visible !== 0;
}

search.addEventListener('input', filterCards);

form.addEventListener('submit', function(event) {
    event.preventDefault();
    filterCards();
});
</script>

</body>
</html>
