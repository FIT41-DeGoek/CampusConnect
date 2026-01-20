<?php
/**
 * Plugin Name: UR Nav Visibility (Text Fallback) - documented
 * Description: Blendet im Frontend Menülinks abhängig vom Login-Status aus (Login/Registration vs My Account).
 * Version: 1.0.2
 *
 * ================================
 * WAS DAS PLUGIN MACHT (einfach)
 * ================================
 * Du hast im Header-Menü (Navigation) die Punkte:
 *  - Login
 *  - Registration (oder Registrieren)
 *  - My Account
 *
 * Du willst:
 *  - WENN eingeloggt:   Login + Registration sollen verschwinden, My Account soll sichtbar bleiben
 *  - WENN ausgeloggt:   My Account soll verschwinden, Login + Registration sollen sichtbar bleiben
 *
 * Problem:
 *  - In Block-Themes/Navigation kommen Links oft nicht als saubere "href=/login/" URLs,
 *    sondern z.B. als "#" oder Buttons/JS.
 *  - Deshalb funktioniert CSS wie a[href*="login"] bei dir nicht zuverlässig.
 *
 * Lösung hier:
 *  - Wir prüfen den Login-Status über die WordPress Body-Klasse im Frontend:
 *      body.logged-in  (eingeloggt)
 *      body.logged-out (ausgeloggt)
 *  - Und wir verstecken die Menüelemente NICHT über URL, sondern über den sichtbaren Text:
 *      "Login", "Registration", "Registrieren", "My Account" usw.
 *
 * WICHTIG:
 *  - Das wirkt NUR im Frontend (http://localhost:8080/).
 *  - Im Website-Editor (Backend) siehst du IMMER alles – das ist normal und gewollt.
 */

if ( ! defined('ABSPATH') ) exit;

/**
 * ================================
 * HIER KANNST DU TEXTE ANPASSEN
 * ================================
 * Falls deine Menüeinträge anders heißen (z.B. "Anmelden" statt "Login"),
 * trägst du das hier in die Listen ein (alles klein schreiben!).
 *
 * hideWhenLoggedIn  = Diese Wörter werden versteckt, WENN du eingeloggt bist.
 * hideWhenLoggedOut = Diese Wörter werden versteckt, WENN du ausgeloggt bist.
 */

/**
 * ================================
 * DER EIGENTLICHE CODE
 * ================================
 * Wir hängen ein kleines Script ans Ende (Footer) der Seite.
 * - Kein Redirect
 * - Kein Observer/Loop
 * - Nur 1x beim Laden
 *
 * Das Script sucht im Header/Nav alle <a> und <button> Elemente.
 * Dann prüft es den Text und blendet die passenden aus.
 */
add_action('wp_footer', function () { ?>
<script>
(function(){

  // 1) Prüfen ob der Benutzer eingeloggt ist:
  // WordPress setzt automatisch diese Klassen am <body>.
  var isLoggedIn = document.body && document.body.classList.contains('logged-in');

  // 2) Welche Texte sollen WEG sein, wenn eingeloggt?
  // -> Login & Registration sollen dann NICHT sichtbar sein.
  var hideWhenLoggedIn  = ['login','anmelden','registration','registrieren','registrierung','register'];

  // 3) Welche Texte sollen WEG sein, wenn ausgeloggt?
  // -> My Account soll dann NICHT sichtbar sein.
  var hideWhenLoggedOut = ['my account','myaccount','mein konto','konto','dashboard','courses','anmeldung-fuer-kursleiter'];

  // 4) Text vereinheitlichen (Trim, klein, doppelte Leerzeichen entfernen)
  function norm(t){
    return (t || '').replace(/\s+/g,' ').trim().toLowerCase();
  }

  // 5) Wir greifen NUR den Header/Nav an (damit wir nicht im Seiteninhalt rumfuschen)
  var scope = document.querySelector('header') || document.querySelector('nav') || document.body;
  if (!scope) return;

  // 6) Alle Links und Buttons im Header/Nav holen
  var nodes = scope.querySelectorAll('a, button');

  // 7) Durchlaufen und je nach Login-Status ausblenden
  for (var i = 0; i < nodes.length; i++) {
    var el = nodes[i];
    var text = norm(el.textContent);

    if (!text) continue;

    // Wenn eingeloggt -> Login/Registration verstecken
    if (isLoggedIn && hideWhenLoggedIn.indexOf(text) !== -1) {
      el.style.display = 'none';
      el.style.visibility = 'hidden';
    }

    // Wenn ausgeloggt -> My Account verstecken
    if (!isLoggedIn && hideWhenLoggedOut.indexOf(text) !== -1) {
      el.style.display = 'none';
      el.style.visibility = 'hidden';
    }
  }

})();
</script>
<?php }, 999);
