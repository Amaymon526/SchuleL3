<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SchuleL3 - Übersicht</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            margin-bottom: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 3em;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 10px;
        }

        .header p {
            color: #666;
            font-size: 1.2em;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .category-box {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .category-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.3);
        }

        .category-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid #f0f0f0;
        }

        .category-icon {
            font-size: 2.5em;
        }

        .category-title {
            font-size: 1.5em;
            font-weight: bold;
            color: #333;
        }

        .dropdown {
            position: relative;
            margin-bottom: 15px;
        }

        .dropdown-label {
            font-weight: 600;
            color: #555;
            margin-bottom: 8px;
            display: block;
        }

        select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #ddd;
            border-radius: 10px;
            font-size: 1em;
            background: white;
            cursor: pointer;
            transition: border-color 0.3s;
        }

        select:hover {
            border-color: #667eea;
        }

        select:focus {
            outline: none;
            border-color: #764ba2;
        }

        .go-button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1em;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .go-button:hover {
            transform: scale(1.02);
        }

        .quick-links {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .quick-links h2 {
            margin-bottom: 20px;
            color: #333;
        }

        .link-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 10px;
        }

        .quick-link {
            padding: 12px 15px;
            background: #f8f9fa;
            border-radius: 8px;
            text-decoration: none;
            color: #333;
            transition: background 0.3s, transform 0.2s;
            display: block;
            text-align: center;
        }

        .quick-link:hover {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            transform: translateX(5px);
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            color: white;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📚 SchuleL3</h1>
            <p>Alle PHP-Beispiele und Projekte auf einen Blick</p>
        </div>

        <div class="grid">
            <!-- Grundlagen -->
            <div class="category-box">
                <div class="category-header">
                    <span class="category-icon">📝</span>
                    <span class="category-title">PHP Grundlagen</span>
                </div>
                <div class="dropdown">
                    <label class="dropdown-label">Seite auswählen:</label>
                    <select id="grundlagen-select">
                        <option value="">-- Bitte wählen --</option>
                        <option value="echo.php">Echo Beispiele</option>
                        <option value="if.php">If/Else Bedingungen</option>
                        <option value="variablen.php">Variablen</option>
                        <option value="get_post.php">GET/POST Formulare</option>
                        <option value="htmlTemplate.php">HTML Template</option>
                    </select>
                </div>
                <button class="go-button" onclick="navigate('grundlagen-select')">→ Zur Seite</button>
            </div>

            <!-- Datenbank -->
            <div class="category-box">
                <div class="category-header">
                    <span class="category-icon">🗄️</span>
                    <span class="category-title">Datenbank (PDO)</span>
                </div>
                <div class="dropdown">
                    <label class="dropdown-label">Beispiel auswählen:</label>
                    <select id="datenbank-select">
                        <option value="">-- Bitte wählen --</option>
                        <option value="datenbank_beispiele/01_verbindung.php">1. Verbindung herstellen</option>
                        <option value="datenbank_beispiele/02_tabelle_erstellen.php">2. Tabelle erstellen</option>
                        <option value="datenbank_beispiele/03_daten_einfuegen.php">3. Daten einfügen (INSERT)</option>
                        <option value="datenbank_beispiele/04_daten_auflisten.php">4. Daten auslesen (SELECT)</option>
                        <option value="datenbank_beispiele/05_daten_suchen.php">5. Daten suchen (WHERE)</option>
                        <option value="datenbank_beispiele/06_schleife_alle_treffer.php">6. Alle Treffer durchgehen</option>
                        <option value="datenbank_beispiele/07_update_delete.php">7. UPDATE & DELETE</option>
                        <option value="datenbank_beispiele/08_erweiterte_funktionen.php">8. COUNT, MAX, MIN, AVG</option>
                        <option value="datenbank_beispiele/09_transaktionen_fehler.php">9. Transaktionen</option>
                    </select>
                </div>
                <button class="go-button" onclick="navigate('datenbank-select')">→ Zur Seite</button>
            </div>

            <!-- Sessions & Cookies -->
            <div class="category-box">
                <div class="category-header">
                    <span class="category-icon">🍪</span>
                    <span class="category-title">Sessions & Cookies</span>
                </div>
                <div class="dropdown">
                    <label class="dropdown-label">Beispiel auswählen:</label>
                    <select id="session-select">
                        <option value="">-- Bitte wählen --</option>
                        <option value="session_cookie_beispiele/01_session_grundlagen.php">1. Session Grundlagen</option>
                        <option value="session_cookie_beispiele/02_session_login.php">2. Login-System</option>
                        <option value="session_cookie_beispiele/03_geschuetzte_seite.php">3. Geschützte Seite</option>
                        <option value="session_cookie_beispiele/04_session_warenkorb.php">4. Warenkorb</option>
                        <option value="session_cookie_beispiele/05_cookie_grundlagen.php">5. Cookie Grundlagen</option>
                        <option value="session_cookie_beispiele/06_cookie_remember_me.php">6. Remember Me</option>
                        <option value="session_cookie_beispiele/07_cookie_praktisch.php">7. Praktische Anwendungen</option>
                        <option value="session_cookie_beispiele/08_session_sicherheit.php">8. Sicherheit</option>
                        <option value="session_cookie_beispiele/10_session_loeschen.php">10. Session löschen</option>
                    </select>
                </div>
                <button class="go-button" onclick="navigate('session-select')">→ Zur Seite</button>
            </div>

            <!-- Login-Systeme -->
            <div class="category-box">
                <div class="category-header">
                    <span class="category-icon">🔐</span>
                    <span class="category-title">Login-Systeme</span>
                </div>
                <div class="dropdown">
                    <label class="dropdown-label">Variante auswählen:</label>
                    <select id="login-select">
                        <option value="">-- Bitte wählen --</option>
                        <option value="pages/login/pdo/login.php">PDO ohne Hash - Login</option>
                        <option value="pages/login/pdo/register.php">PDO ohne Hash - Register</option>
                        <option value="pages/login/pdo_hash/login.php">PDO mit Hash - Login</option>
                        <option value="pages/login/pdo_hash/register.php">PDO mit Hash - Register</option>
                        <option value="pages/login/txt/login.php">TXT ohne Hash - Login</option>
                        <option value="pages/login/txt/register.php">TXT ohne Hash - Register</option>
                        <option value="pages/login/txt_hash/login.php">TXT mit Hash - Login</option>
                        <option value="pages/login/txt_hash/register.php">TXT mit Hash - Register</option>
                    </select>
                </div>
                <button class="go-button" onclick="navigate('login-select')">→ Zur Seite</button>
            </div>

            <!-- Newsletter -->
            <div class="category-box">
                <div class="category-header">
                    <span class="category-icon">📧</span>
                    <span class="category-title">Newsletter</span>
                </div>
                <div class="dropdown">
                    <label class="dropdown-label">Variante auswählen:</label>
                    <select id="newsletter-select">
                        <option value="">-- Bitte wählen --</option>
                        <option value="pages/newsletter_pdo/anmelden.php">Mit Datenbank (PDO)</option>
                        <option value="pages/newsletter_txt/anmelden.php">Mit Textdatei (TXT)</option>
                    </select>
                </div>
                <button class="go-button" onclick="navigate('newsletter-select')">→ Zur Seite</button>
            </div>

            <!-- Öffnungszeiten -->
            <div class="category-box">
                <div class="category-header">
                    <span class="category-icon">🕐</span>
                    <span class="category-title">Öffnungszeiten</span>
                </div>
                <div class="dropdown">
                    <label class="dropdown-label">Variante auswählen:</label>
                    <select id="oeffnung-select">
                        <option value="">-- Bitte wählen --</option>
                        <option value="pages/oeffnungszeiten/index_pdo.php">Mit Datenbank (PDO)</option>
                        <option value="pages/oeffnungszeiten/index_txt.php">Mit Textdatei (TXT)</option>
                    </select>
                </div>
                <button class="go-button" onclick="navigate('oeffnung-select')">→ Zur Seite</button>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="quick-links">
            <h2>📖 Dokumentationen</h2>
            <div class="link-grid">
                <a href="datenbank_beispiele/README.md" class="quick-link">Datenbank README</a>
                <a href="session_cookie_beispiele/README.md" class="quick-link">Session/Cookie README</a>
                <a href="pages/login/README.md" class="quick-link">Login README</a>
            </div>
        </div>

        <div class="footer">
            <p>SchuleL3 - PHP Lernprojekt © 2025</p>
        </div>
    </div>

    <script>
        function navigate(selectId) {
            const select = document.getElementById(selectId);
            const url = select.value;

            if (url) {
                window.location.href = url;
            } else {
                alert('Bitte wähle zuerst eine Seite aus!');
            }
        }

        // Enter-Taste in Dropdowns ermöglichen
        document.querySelectorAll('select').forEach(select => {
            select.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    const button = this.parentElement.nextElementSibling;
                    button.click();
                }
            });
        });
    </script>
</body>
</html>
