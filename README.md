# Alumni Connect — startproject

## Installeren
1. Installeer XAMPP en start Apache en MySQL.
2. Kopieer de map `alumni_connect` naar `C:\xampp\htdocs\`.
3. Open `http://localhost/phpmyadmin` → Importeren → kies `database/schema.sql`.
4. Controleer database-inloggegevens in `config/db.php` (standaard root zonder wachtwoord).
5. Open `http://localhost/alumni_connect/`.
6. Registreer een account en log in.

## Testrollen instellen
Nieuwe accounts krijgen de rol `student`. Voor lokaal testen: ga in phpMyAdmin naar `alumni_connect` → `users` → Bewerk en wijzig `role` naar `alumnus`, `bedrijf`, `organisator`, `mentor` of `beheerder`. **Doe dit alleen lokaal**, nooit als publiek toegankelijke functie.

## Wat werkt
Registratie, inloggen, uitloggen, eigen account bekijken/wijzigen/verwijderen en generieke create/read/update/delete schermen voor negen extra entiteiten met rolcontrole voor aanmaken en eigenaarcontrole voor wijzigen/verwijderen.

## Nog te bouwen vóór definitieve oplevering
Dit is een MVP/startproject, **niet alle acceptance criteria zijn af**. Belangrijk: voeg autorisatie voor lezen van private mentor-matches, portfoliofeedback en aanmeldingen toe; controleer gekoppelde IDs en rolrelaties; gebruik keuzelijsten in plaats van vrije ID-velden; voeg eventregistratie via knop toe; voeg een echt portfolio-entiteit toe; voeg datum-/veldvalidatie toe; maak autorisatie voor organisatoren en bedrijven sluitend; voeg paginering en zoekfuncties toe; implementeer accountverwijderbeleid en tests. Publiceer deze versie niet online zonder die verbeteringen.
