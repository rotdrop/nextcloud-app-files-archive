OC.L10N.register(
    "files_archive",
    {
    "File or folder could not be found." : "Datei oder Ordner konnte nicht gefunden werden.",
    "The user folder for user \"%s\" could not be opened." : "Der Benutzer Ordner für \"%s\" konnte nicht geöffnet werden.",
    "The archive file \"%s\" could not be found on the server." : "Die Archiv-Datei \"%s\" konnte auf dem Server nicht gefunden werden.",
    "Unable to open the archive file \"%s\": %s." : "Kann die Archiv-Datei \"%s\" nicht öffnen: %s.",
    "Error: %s" : "Fehler: %s",
    "Unable to open the archive file \"%s\"." : "Kann die Archiv-Datei \"%s\" nicht öffnen.",
    "The archive file \"%1$s\" appears to be a zip-bomb: uncompressed size %2$s > admin limit %3$s." : "Die Archiv-Datei \"%1$s\" scheint eine ZIP-Bombe zu sein: dekomprimierte Größe %2$s > Administrations-Limit %3$s.",
    "The archive file \"%1$s\" is too large: uncompressed size %2$s > user limit %3$s." : "Die Archiv-Datei \"%1$s\" ist zu groß: dekomprimierte Größe %2$s > Benutzer-Limit %3$s.",
    "Unable to open the target parent folder \"%s\"." : "Kann den Eltern-Ordner \"%s\" nicht öffnen.",
    "The target folder \"%s\" already exists and auto-rename is not enabled." : "Der Ziel-Ordner \"%s\" existiert bereits und automatische Umbenennung ist nicht aktiviert.",
    "Unable to extract \"%1$s\" to \"%2$s\": \"%3$s\"." : "Kann \"%1$s\" nicht an \"%2$s\" entpacken: \"%3$s\".",
    "Extracting \"%1$s\" to \"%2$s\" succeeded." : "Das Entpacken von \"%1$s\" an \"%2$s\" war erfolgreich.",
    "Archive background mount job scheduled successfully." : "Hintergrundbereitstellungsjob für Archivierung erfolgreich geplant.",
    "Archive background extraction job scheduled successfully." : "Hintergrundextraktionsjob für das Archiv erfolgreich geplant.",
    "Cancelling %s-job for archive file \"%s\" failed." : "Das Abbrechen des %s-Jobs für die Archivdatei \"%s\" ist fehlgeschlagen.",
    "Post to endpoint \"%s\" not implemented." : "POST-Anfragen an \"%s\" sind nicht implementiert.",
    "Post to base URL of app \"%s\" not allowed." : "POST-Anfragen an die Basis-Adresse der App \"%s\" sind nicht erlaubt.",
    "Get from endpoint \"%s\" not implemented." : "GET-Anfragen an \"%s\" sind nicht implementiert.",
    "\"%1$s\" is already mounted on \"%2$s\"." : "\"%1$s\" ist bereits an \"%2$s\" eingehängt.",
    "Unable to open parent folder \"%1$s\" of mount point \"%2$s\": %3$s." : "Kann den Eltern-Ordner \"%1$s\" des Einhängepunkts \"%2$s\" nicht öffnen: %3$s.",
    "The mount point \"%s\" already exists and auto-rename is not enabled." : "Der Einhängepunkt \"%s\" existiert bereits und automatische Konfliktauflösung ist nicht aktiviert.",
    "Unable to update the file cache for the mount point \"%1s\": %2$s." : "Kann den Datei-Cache für den Einhängepunkt \"%1s\" nicht aktualisieren: %2$s.",
    "\"%s\" is not mounted." : "\"%s\" ist nicht eingehängt.",
    "Directory \"%s\" is not a mount point." : "Verzeichnis \"%s\" ist kein Einhängepunkt.",
    "Archive \"%1$s\" has been unmounted from \"%2$s\"." : "Das Archiv \"%1$s\" wurden von Einhängepunkt \"%2$s\" getrennt.",
    "Only the passphrase may be changed for an existing mount." : "Ausschließlich das Verschlüsselungspasswort kann für einen aktiven Einhängepunkt geändert werden.",
    "Unknown admin setting: \"%1$s\"" : "Unbekannte Administrationseinstellung: \"%1$s\"",
    "The admin setting \"%1$s\" is read-only" : "Die Administrationseinstellung \"%1$s\" kann nur gelesen werden",
    "Unknown personal setting: \"%1$s\"" : "Unbekannte persönliche Einstellung: \"%1$s\"",
    "The personal setting \"%1$s\" is read-only" : "Die persönliche Einstellung \"%1$s\" kann nur gelesen werden.",
    "Value \"%1$s\" for setting \"%2$s\" is not convertible to boolean." : "Der Wert \"%1$s\" für die Einstellung \"%2$s\" ist nicht in einen booleschen Wert konvertierbar.",
    "The target folder template \"%1$s\" must contain the archive file placeholder \"%2$s\"." : "Die Schablone für den Zielordnernamen \"%1$s\" muss den Platzhalter \"%2$s\" für den Archivnamen enthalten.",
    "Unknown personal setting: \"%s\"." : "Unbekannte persönliche Einstellung: \"%s\"",
    "Unable to parse memory size limit \"%s\"" : "Kann das Speicher-Limit nicht interpretieren: \"%s\"",
    "Archive Manager" : "Archiv-Manager",
    "The archive file {source} will be mounted as a virtual folder at {destination}." : "Die Archivdatei {source} wird als virtueller Ordner unter {destination} eingehängt.",
    "The archive file {source} will be extracted to {destination}." : "Die Archivdatei {source} wird nach {destination} entpackt.",
    "Archive mount of {source} succeeded." : "Archiveinbindung von {source} erfolgreich.",
    "Archive extraction of {source} succeeded." : "Archiv-Extraktion von {source} erfolgreich.",
    "Your archive file {source} has been mounted as a virtual folder at {destination}. In order to unmount it it is safe to just delete the mount point {destination}. This will do no harm to the archive file and just issue an \"unmount\" action. Please note that the virtual folder is read-only." : "Ihre Archivdatei {source} wurde als virtueller Ordner unter {destination} bereitgestellt. Um die Bereitstellung aufzuheben, können Sie einfach den Bereitstellungspunkt {destination} löschen. Dadurch wird die Archivdatei nicht beschädigt, es wird lediglich eine \"Unmount\"-Aktion ausgeführt. Bitte beachten Sie, dass der virtuelle Ordner schreibgeschützt ist.",
    "Your archive file {source} has been extracted to {destination}. The archive contents reside there as ordinary files. Please feel free to use those as it pleases you. There is not automatic cleanup, the extracted files will remain in your file-space until you delete them manually. Just delete the folder {destination} if you have no more use of those files. Please note also that any changes committed to the extracted files will not be written back to the archive file {source}." : "Ihre Archivdatei {source} wurde nach {destination} entpackt. Die Archivinhalte liegen dort als gewöhnliche Dateien vor. Sie können diese nach Belieben verwenden. Es erfolgt keine automatische Bereinigung. Die entpackten Dateien verbleiben in Ihrem Dateibereich, bis sie manuell gelöscht werden. Wenn die Dateien nicht mehr benötigt werden, löschen Sie einfach den Ordner {destination}. Bitte beachten Sie auch, dass an den entpackten Dateien vorgenommene Änderungen nicht in die Archivdatei {source} zurückgeschrieben werden.",
    "Mounting {source} at {destination} has failed: {message}" : "Einhängen von {source} unter {destination} ist fehlgeschlagen: {message}",
    "Extracting {source} to {destination} has failed: {message}" : "Fehler beim Extrahieren von {source} nach {destination}: {message}",
    "Mounting {source} at {destination} has failed." : "Einhängen von {source} unter {destination} ist fehlgeschlagen.",
    "Extacting {source} to {destination} bas failed." : "Das Entpacken von {source} nach {destination} ist fehlgeschlagen.",
    "Unsupported subject: \"%s\"." : "Nicht unterstützter Betreff: \"%s\".",
    "Archive Explorer" : "Archiv-Explorer",
    "Unable to detect the archive format of \"%1$s\"." : "Archivformat von \"%1$s\" kann nicht erkannt werden.",
    "Archive format of \"%1$s\" detected as \"%2$s\", but there is no backend driver installed which can decompress \".%3$s\" files." : "Das Archivformat von \"%1$s\" wurde als \"%2$s\" erkannt, aber es ist kein Backend-Treiber installiert, der \".%3$s\"-Dateien entpacken kann.",
    "Unable to deal with tar-files. Please check the installation of the app." : "Tar-Dateien können nicht verarbeitet werden. Bitte überprüfen Sie die Installation der App.",
    "The archive format of \"%1$s\" has been detected as \"%2$s\", but there is no backend driver installed which can deal with this format." : "Das Archivformat von \"%1$s\" wurde als \"%2$s\" erkannt, aber es ist kein Backend-Treiber installiert, der mit diesem Format umgehen kann.",
    "The \"%1$s\" driver could handle this format, but it is not installed." : "Der \"%1$s\"-Treiber kann dieses Format verarbeiten, ist aber nicht installiert.",
    "Installation instructions: " : "Installationsanleitung:",
    "The \"%1$s\" driver claims to handle this format, but cannot extract the archive content." : "Der \"%1$s\"-Treiber gibt an, dieses Format verarbeiten zu können, kann den Archivinhalt jedoch nicht entpacken.",
    "Unable to open archive file %s (%s)" : "Kann die Archive Datei %s (%s) nicht öffnen",
    "Uncompressed size of archive \"%1$s\" is too large: %2$s > %3$s" : "Die Größe des dekomprimierten Archivs \"%1$s\" ist zu groß:  %2$s > %3$s",
    "There is no archive file associated with this archiver instance." : "Dieser Archivierungsinstanz ist keine Archivdatei zugeordnet.",
    "Installation problem; the required resource \"%1$s\" of type \"%2$s\" is not installed on the server, please contact the system administrator!" : "Installationsproblem; die benötigte Ressource \"%1$s\" vom Typ \"%2$s\" ist auf dem Server nicht installiert, bitte die Systemadministration kontaktieren!",
    "User" : "Benutzer",
    "Password" : "Passwort",
    "Login succeeded." : "Anmeldung erfolgreich.",
    "Login failed." : "Anmeldung fehlgeschlagen.",
    "Error, caught an exception." : "Fehler, Ausnahme abgefangen.",
    "Caused by previous exception" : "Verursacht durch eine vorhergehende Ausnahme",
    "bytes" : "Bytes",
    "The supplied color-string \"%s\" seems to be invalid." : "Die angegebene Farbzeichenfolge \"%s\" scheint ungültig zu sein.",
    "The input color values are invalid." : "Die eingegebenen Farbwerte sind ungültig.",
    "On-the-fly archive inspector for Nextcloud" : "On-the-Fly-Archivinspektor für Nextcloud",
    "Inspect, mount and extract archive files (zip, tar, etc.)" : "Archiv-Dateien (zip, tar, …) als virtuelle Speicher einhängen und entpacken.",
    "pick a color" : "Farbe wählen",
    "open" : "öffnen",
    "submit" : "Übermitteln",
    "revert color" : "Farbe wiederherstellen",
    "restore palette" : "Palette wiederherstellen",
    "factory reset palette" : "Werksreset der Palette",
    "Custom Color" : "Benutzerdefinierte Farbe",
    "Choose a folder" : "Ordner auswählen",
    "Choose a prefix-folder" : "Präfixordner auswählen",
    "Invalid path selected: \"{dir}\"." : "Es wurde ein ungültiger Pfad gewählt: \"{dir}\".",
    "Selected path: \"{dir}/{base}/\"." : "Ausgewählter Pfad: \"{dir}/{base}\".",
    "Please select an item!" : "Bitte ein Element wählen!",
    "An empty value is not allowed, please make your choice!" : "Ein leerer Wert ist unzulässig, bitte treffen Sie Ihre Wahl!",
    "Click to submit your changes." : "Klicken um Ihre Änderungen zu übermitteln",
    "Reset Changes" : "Änderungen zurücksetzen",
    "Clear Selection" : "Auswahl leeren",
    "Unable to query the archive-format support matrix." : "Die Archivformat -Unterstützungsmatrix kann nicht abgefragt werden.",
    "Unable to query the information about the available archive backend drivers." : "Die Informationen zu den verfügbaren Archiv-Backend-Treibern können nicht abgefragt werden.",
    "Archive Manager, Admin Settings" : "Archivmanager, Administrationseinstellungen",
    "Archive Extraction" : "Archiv entpacken",
    "Archive Size Limit" : "Größenbeschränkung für Archive",
    "Disallow archive extraction for archives with decompressed size larger than this limit." : "Verbiete das Entpacken von Archivdateien, deren Größe nach der Dekomprimierung größer als diese Grenze ist.",
    "Diagnostics" : "Diagnose",
    "Archive Formats" : "Archiv-Formate",
    "Supported Drivers" : "Unterstützte Treiber",
    "Archive Manager, Personal Settings" : "Archivmanager, Persönliche Einstellungen",
    "Security Options" : "Sicherheitseinstellungen",
    "Administrative size limit: {value}" : "Größenbegrenzung durch die Administration: {value}",
    "Mount Options" : "Einhänge-Optionen",
    "Template for the default name of the mount point" : "Vorlage für den Standardnamen des Einhängepunkts.",
    "{archiveFileName} will be replaced by the filename of the archive file without extensions." : "{archiveFileName} wird durch den Dateinamen der Archivdatei ohne Dateinamenserweiterungen ersetzt.",
    "strip common path prefix by default" : "Standardmäßig das allgemeine Pfadpräfix entfernen",
    "automatically change the mount point name if it already exists" : "Namen des Einhängepunktes automatisch ändern, falls der Name bereits verwendet wird",
    "default to scheduling mount requests as background job" : "Standardmäßig werden Einhänge-Anforderungen als Hintergrundjob geplant",
    "Extraction Options" : "Einstellungen für das Entpacken",
    "Template for the default name of the extraction folder" : "Vorlage für den Standardnamen des Ordners für das Entpacken",
    "automatically change the target folder name if the target folder already exists" : "Den Namen des Ziel-Ordners automatisch ändern, falls der Ordner bereits vorhanden ist",
    "default to scheduling extraction requests as background job" : "Standardmäßig werden Extraktionsanfragen als Hintergrundjob geplant",
    "unknown" : "unbekannt",
    "ok" : "Ok",
    "zip bomb" : "ZIP-Bombe",
    "too large" : "zu groß",
    "Mount Points" : "Einhänge-Punkte",
    "not mounted" : "nicht eingehängt",
    "Cancelling the background job failed with error {status}, \"{statusText}\"." : "Das Abbrechen der Hintergrundaufgabe ist mit dem Fehler {status}, \"{statusText}\" fehlgeschlagen.",
    "Mount request failed with error {status}, \"{statusText}\"." : "Einhänge-Anfrage ist fehlgeschlagen, Fehler-Code {status}, \"{statusText}\".",
    "Unmount request failed with error {status}, \"{statusText}\"." : "Aushänge-Anfrage ist fehlgeschlagen, Fehler-Code {status}, \"{statusText}\".",
    "Archive extraction failed with error {status}, \"{statusText}\"." : "Entpacken des Archivs fehlgeschlagen mit Fehler {status}, \"{statusText}\".",
    "Patching the passphrase failed with error {status}, \"{statusText}\"." : "Das Ändern des Passworts für aktive Einhängepunkte ist fehlgeschlagen, Fehler-Code {status}, \"{statusText}\".",
    "Archive Information" : "Archiv-Informationen",
    "archive status" : "Archiv-Status",
    "archive format" : "Archiv-Format",
    "MIME type" : "MIME-Typ",
    "backend driver" : "Backend-Treiber",
    "uncompressed size" : "Unkomprimierte Größe",
    "compressed size" : "komprimierte Größe",
    "archive file size" : "Archiv-Dateigröße",
    "# archive members" : "Archiv-Mitglieder",
    "common prefix" : "Gemeinsamer Präfix",
    "creator's comment" : "Ersteller-Kommentar",
    "Passphrase" : "Passphrase",
    "unset" : "nicht gesetzt",
    "archive passphrase" : "Archiv-Passphrase",
    "Show Passphrase" : "Passphrase anzeigen",
    "Disconnect storage" : "Speicher trennen",
    "Common prefix {prefix} is stripped." : "Gemeinsamer Präfix {prefix} wurde entfernt.",
    "Not mounted, create a new mount point:" : "Nicht eingehängt, einen neuen Einhänge-Punkt erzeugen:",
    "base name" : "Basisname",
    "strip common path prefix" : "gemeinensamen Dateipfad-Präfix entfernen",
    "schedule as background job" : "Als Hintergrundjob planen",
    "Extract Archive" : "Archiv extrahieren",
    "Choose a directory to extract the archive to:" : "Ordner wählen, in den das Archiv extrahiert wird:",
    "basename" : "Basisname",
    "Pending Background Jobs" : "Ausstehende Hintergrundaufgaben",
    "No Pending Background Jobs" : "Keine ausstehenden Hintergrundaufgaben",
    "Cancel Job" : "Aufgabe abbrechen",
    "mount" : "Einbinden",
    "extract" : "Entpacken",
    "Common prefix {prefix} will be stripped." : "Gemeinsamer Präfix {prefix} wird entfernt werden.",
    "No pending background job." : "Keine ausstehende Hintergrundaufgabe",
    "Job type: {type}" : "Auftragstyp: {type}.",
    "Mount Archive" : "Archiv einhängen",
    "The archive \"{archivePath}\" is already mounted on \"{mountPointPath}\"." : "Die Archiv-Datei \"{archivePath}\" ist bereits an der Stelle \"{mountPointPath}\" eingehängt.",
    "The archive \"{archivePath}\" has been mounted on \"{mountPointPath}\"." : "Das Archiv \"{archivePath}\" wurde unter \"{mountPointPath}\" eingebunden.",
    "Failed to mount archive file \"{archivePath}: {message}\"." : "Fehler beim Einbinden der Archivdatei \"{archivePath}: {message}\".",
    "Failed to mount archive file \"{archivePath}\"." : "Fehler beim Einbinden der Archivdatei \"{archivePath}\".",
    "Unable to obtain mount status for archive file \"{archivePath}: {message}\"." : "Einbindungsstatus für das Archiv \"{archivePath}: {message}\" konnte nicht abgerufen werden.",
    "Unable to obtain mount status for archive file \"{archivePath}\"." : "Bereitstellungsstatus für Archivdatei \"{archivePath}\" kann nicht abgerufen werden.",
    "Archive" : "Archiv",
    "reason unknown" : "Grund unbekannt",
    "Unable to query the initial value of all settings: {message}" : "Kann die anfänglichen Werte der Einstellungen nicht abrufen: {message}",
    "Unable to query the initial value of {settingsKey}: {message}" : "Kann die anfänglichen Wert der Einstellung {settingsKey} nicht abrufen: {message}",
    "true" : "wahr",
    "Successfully set \"{settingsKey}\" to {value}." : "Der Wert für die Einstellung \"{settingsKey}\" wurde erfolgreich auf {value} gesetzt.",
    "Setting \"{settingsKey}\" has been unset successfully." : "Die Einstellung \"{settingsKey}\" wurde erfolgreich gelöscht.",
    "Unable to set \"{settingsKey}\" to {value}: {message}." : "Konnte den Wert für die Einstellung \"{settingsKey}\" nicht auf den Wert \"{value}\" setzen: {message}",
    "false" : "falsch",
    "Unable to unset \"{settingsKey}\": {message}" : "Kann die Einstellung \"{settingsKey}\" nicht löschen: {message}",
    "Confirmation Required" : "Bestätigung erforderlich",
    "Unconfirmed, reverting to old value." : "Bestätigung nicht erteilt, setze auf den alten Wert zurück.",
    "Successfully set value for \"{settingsKey}\" to \"{displayValue}\"" : "Der Wert für die Einstellung \"{settingsKey}\" wurde erfolgreich auf \"{displayValue}\" gesetzt",
    "Setting \"{setting}\" has been unset successfully." : "Die Einstellung \"{setting}\" wurde erfolgreich gelöscht.",
    "Could not set value for \"{settingsKey}\" to \"{value}\": {message}" : "Konnte den Wert für die Einstellung \"{settingsKey}\" nicht auf den Wert \"{value}\" setzen: {message}",
    "OK" : "OK",
    "Created" : "Erstellt",
    "Accepted" : "Akzeptiert",
    "Non-Authoritative Information" : "Unverbindliche Information",
    "No Content" : "Kein Inhalt",
    "Reset Content" : "Inhalt zurückgesetzt",
    "Partial Content" : "Unvollständige Antwort",
    "Multi-Status (WebDAV)" : "Multi-Status (WebDAV)",
    "Already Reported (WebDAV)" : "Bereits berichtet (WebDAV)",
    "IM Used" : "IM verwendet",
    "Multiple Choices" : "Mehrere Wahlmöglichkeiten",
    "Moved Permanently" : "Dauerhaft verschoben",
    "Found" : "Gefunden",
    "See Other" : "Siehe andere Web-Adresse",
    "Not Modified" : "Unverändert",
    "Use Proxy" : "Proxy benutzen",
    "(Unused)" : "(Unbenutzt)",
    "Temporary Redirect" : "Vorübergehende Umleitung",
    "Permanent Redirect (experimental)" : "Permanente Umleitung (experimentell)",
    "Bad Request" : "Ungültige Anfrage",
    "Unauthorized" : "Nicht autorisiert",
    "Payment Required" : "Bezahlung erforderlich",
    "Forbidden" : "Verboten",
    "Not Found" : "Nicht gefunden",
    "Method Not Allowed" : "Methode unzulässig",
    "Not Acceptable" : "Nicht akzeptabel",
    "Proxy Authentication Required" : "Proxy-Anmeldung erforderlich",
    "Request Timeout" : "Anfrage-Zeitüberschreitung",
    "Conflict" : "Konflikt",
    "Gone" : "Weg",
    "Length Required" : "Länge benötigt",
    "Precondition Failed" : "Vorbedingung nicht erfüllt",
    "Request Entity Too Large" : "Anfrageeinheit zu groß",
    "Request-URI Too Long" : "Anfrage-URI zu lang",
    "Unsupported Media Type" : "Nicht unterstützter Medien-Typ",
    "Requested Range Not Satisfiable" : "Angeforderter Bereich nicht erfüllbar",
    "Expectation Failed" : "Erwartung fehlgeschlagen",
    "I'm a teapot (RFC 2324)" : "Ich bin eine Teekanne (RFC 2324)",
    "Enhance Your Calm (Twitter)" : "Verstärke Deine Ruhe (Twitter)",
    "Unprocessable Entity (WebDAV)" : "Nicht verarbeitbare Entität (WebDAV)",
    "Locked (WebDAV)" : "Gesperrt (WebDAV)",
    "Failed Dependency (WebDAV)" : "Fehlgeschlagene Abhängigkeit (WebDAV)",
    "Reserved for WebDAV" : "Reserviert für WebDAV",
    "Upgrade Required" : "Upgrade erforderlich",
    "Precondition Required" : "Vorbedingung erforderlich",
    "Too Many Requests" : "Zu viele Anfragen.",
    "Request Header Fields Too Large" : "Anfrage-Header-Felder zu groß",
    "No Response (Nginx)" : "Keine Antwort (Nginx)",
    "Retry With (Microsoft)" : "Wiederholen mit (Microsoft)",
    "Blocked by Windows Parental Controls (Microsoft)" : "Von Windows-Kindersicherung blockiert (Microsoft)",
    "Unavailable For Legal Reasons" : "Aus rechtlichen Gründen nicht verfügbar",
    "Client Closed Request (Nginx)" : "Anwender-Programm hat die Anfrage geschlossen (Nginx)",
    "Internal Server Error" : "Interner Serverfehler",
    "Not Implemented" : "Nicht implementiert",
    "Bad Gateway" : "Falsches Gateway",
    "Service Unavailable" : "Dienst nicht verfügbar",
    "Gateway Timeout" : "Gateway-Zeitüberschreitung",
    "HTTP Version Not Supported" : "HTTP-Version nicht unterstützt",
    "Variant Also Negotiates (Experimental)" : "Variante handelt außerdem aus (Experimenmtell)",
    "Insufficient Storage (WebDAV)" : "Zu wenig Speicherplatz (WebDAV)",
    "Loop Detected (WebDAV)" : "Schleife erkannt (WebDAV)",
    "Bandwidth Limit Exceeded (Apache)" : "Bandbreiten-Beschränkung überschritten (Apache)",
    "Not Extended" : "Nicht erweitert",
    "Network Authentication Required" : "Netzwerk-Anmeldung erforderlich",
    "Network read timeout error" : "Netzwerk Lesezeitüberschreitung",
    "Network connect timeout error" : "Netzwerk Verbindungs-Zeitüberschreitung",
    "Operation cancelled by user." : "Vorgang von Benutzer abgebrochen",
    "Aborted" : "Abgebrochen",
    "Error" : "Fehler",
    "System Administrator" : "Systemadministration",
    "Feedback email: {AutoReport}" : "Rückmeldung per Email: {AutoReport}",
    "Something went wrong." : "Irgendetwas ist schief gelaufen.",
    "If it should be the case that you are already logged in for a long time without interacting with the app, then the reason for this error is probably a simple timeout." : "Sollte es vorkommen, dass Sie bereits längere Zeit eingeloggt sind, ohne mit der App zu interagieren, dann ist der Grund für diesen Fehler wahrscheinlich eine einfache Zeitüberschreitung.",
    "In any case it may help to logoff and logon again, as a temporary workaround. You will be redirected to the login page when you close this window." : "Als einstweiligen `Work-Around' kann man in jedem Fall versuchen, sich ab- und wieder anzumelden und den Vorgang zu wiederholen. Wenn Sie dieses Fenster schließen, werden Sie zur Login-Seite weitergeleitet.",
    "Unrecoverable unknown internal error, no details available" : "Nicht behebbarer unbekannter interner Fehler, keine Details verfügbar",
    "Internal Error" : "Interner Fehler",
    "Field {RequiredField} not present in AJAX response." : "In der AJAX-Antwort fehlt das Feld {RequiredField}.",
    "Missing data" : "Fehlende Daten",
    "The submitted data \"{stringValue}\" seems to be a plain string, but we need an object where the data is provided through above listed properties." : "Die übermittelten Daten \"{stringValue}\" scheinen eine einfache Zeichenkette zu sein, benötigt wird jedoch ein JavaScript-Obekt, das die Daten über die oben aufgeführten (fehlenden) Objekt-Eigenschaften zu Verfügung stellt.",
    "Error: plain string received" : "Fehler: einfache Zeichenkette empfangen",
    "The submitted data is not a plain object, and does not provide the properties listed above." : "Die übermittelten Daten sind kein einfaches Objekt und beinhalteten nicht die oben aufgeführten benötigten Eigenschaften.",
    "Error: not a plain object" : "Fehler: kein einfaches Objekt",
    "Unknown JSON error response to AJAX call: {status} / {error}" : "Unbekannte JSON Fehler-Antwort auf AJAX-Anfrage: {status} / {error}",
    "HTTP error response to AJAX call: {code} / {error}" : "HTTP Fehler-Antwort auf AJAX-Anfrage: {code} / {error}",
    "No" : "Nein",
    "Yes" : "Ja",
    "Debug Information" : "Debug Informationen"
},
"nplurals=2; plural=(n != 1);");
