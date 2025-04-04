OC.L10N.register(
    "files_archive",
    {
    "File or folder could not be found." : "Soubor nebo složku se nedaří nalézt.",
    "The user folder for user \"%s\" could not be opened." : "Domovskou složku uživatele „%s“ se nepodařilo otevřít.",
    "The archive file \"%s\" could not be found on the server." : "Soubor s archivem „%s“ nebyl na serveru nalezen.",
    "Unable to open the archive file \"%s\": %s." : "Nedaří se otevřít soubor s archivem „%s: %s.",
    "Error: %s" : "Chyba: %s",
    "Unable to open the archive file \"%s\"." : "Nedaří se otevřít soubor s archivem „%s“.",
    "The archive file \"%1$s\" appears to be a zip-bomb: uncompressed size %2$s > admin limit %3$s." : "Zdá se, že od souboru s archivem „%1$s“ hrozí zlomyslné zahlcení serveru (tzv. zip bomba): rozbalená velikost %2$s přesahuje limit %3$s, určený správcem .",
    "The archive file \"%1$s\" is too large: uncompressed size %2$s > user limit %3$s." : "Soubor s archivem „%1$s“ je příliš velký: velikost po rozbalení %2$s přesahuje limit %3$s, určený uživatelem.",
    "Unable to open the target parent folder \"%s\"." : "Nedaří se otevřít cílovou nadřazenou složku „%s“.",
    "The target folder \"%s\" already exists and auto-rename is not enabled." : "Cílová složka „%s“ už existuje a automatické přejmenovávání není zapnuto.",
    "Unable to extract \"%1$s\" to \"%2$s\": \"%3$s\"." : "Nedaří se rozbalit „%1$s“ do „%2$s“: „%3$s“.",
    "Extracting \"%1$s\" to \"%2$s\" succeeded." : "Rozbalování „%1$s“ do „%2$s“ úspěšné.",
    "Archive background mount job scheduled successfully." : "Úloha pro připojení (mount) na pozadí úspěšně naplánována.",
    "Archive background extraction job scheduled successfully." : "Úloha pro rozbalení na pozadí úspěšně naplánována.",
    "Cancelling %s-job for archive file \"%s\" failed." : "Rušení %s úlohy pro soubor archivu „%s“ se nezdařilo.",
    "Post to endpoint \"%s\" not implemented." : "Odeslání na koncový bod „%s“ neimplementováno.",
    "Post to base URL of app \"%s\" not allowed." : "Odeslání na základní URL aplikace „%s“ nedovoleno.",
    "Get from endpoint \"%s\" not implemented." : "Získání od koncového bodu „%s“ neimplementováno.",
    "\"%1$s\" is already mounted on \"%2$s\"." : "„%1$s“ už je připojené v „%2$s.",
    "Unable to open parent folder \"%1$s\" of mount point \"%2$s\": %3$s." : "Nedaří se otevřít nadřazenou složku „%1$s“ přípojného bodu „%2$s: %3$s.",
    "The mount point \"%s\" already exists and auto-rename is not enabled." : "Přípojný bod „%s“ už existuje a automatické přejmenovávání není zapnuto.",
    "Unable to update the file cache for the mount point \"%1s\": %2$s." : "Nedaří se aktualizovat mezipaměť souborů pro přípojný bod „%1s: %2$s.",
    "\"%s\" is not mounted." : "„%s není připojeno.",
    "Directory \"%s\" is not a mount point." : "Složka „%s“ není přípojným bodem.",
    "Archive \"%1$s\" has been unmounted from \"%2$s\"." : "Archiv „%1$s“ byl odpojen od „%2$s.",
    "Only the passphrase may be changed for an existing mount." : "Pro už existující připojení (mount) je možné měnit pouze heslovou frázi.",
    "Unknown admin setting: \"%1$s\"" : "Neznámé nastavení správy: „%1$s",
    "The admin setting \"%1$s\" is read-only" : "Nastavení správy „%1$s“ je pouze pro čtení",
    "Unknown personal setting: \"%1$s\"" : "Neznámo osobní nastavení: „%1$s“.",
    "The personal setting \"%1$s\" is read-only" : "Osobní nastavení „%1$s“ je pouze pro čtení",
    "Value \"%1$s\" for setting \"%2$s\" is not convertible to boolean." : "Hodnota „%1$s“ pro nastavení „%2$s“ není převoditelná na typ boolean.",
    "The target folder template \"%1$s\" must contain the archive file placeholder \"%2$s\"." : "Je třeba, aby šablona cílové složky „%1$s“ obsahovala zástupné vyjádření souboru archivu „%2$s“.",
    "Unknown personal setting: \"%s\"." : "Neznámo osobní nastavení: „%s“.",
    "Unable to parse memory size limit \"%s\"" : "Nedaří se zpracovat limit velikosti paměti „%s“",
    "Archive Manager" : "Správa archivů",
    "The archive file {source} will be mounted as a virtual folder at {destination}." : "Soubor archivu {source} bude připojen jako virtuální složka v {destination}.",
    "The archive file {source} will be extracted to {destination}." : "Soubor archivu {source} bude rozbalen do {destination}.",
    "Archive mount of {source} succeeded." : "Připojení archivu {source} úspěšné.",
    "Archive extraction of {source} succeeded." : "Rozbalení archivu {source} úspěšné.",
    "Your archive file {source} has been mounted as a virtual folder at {destination}. In order to unmount it it is safe to just delete the mount point {destination}. This will do no harm to the archive file and just issue an \"unmount\" action. Please note that the virtual folder is read-only." : "Soubor s archivem {source} byl připojen jako virtuální složka do {destination}. pro odpojení bezpečně stačí pouze smazat přípojný bod {destination}. Souboru s archivem to neublíží a jen vyvolá akci „odpojit“. Uvědomte si, že virtuální složka je pouze pro čtení.",
    "Your archive file {source} has been extracted to {destination}. The archive contents reside there as ordinary files. Please feel free to use those as it pleases you. There is not automatic cleanup, the extracted files will remain in your file-space until you delete them manually. Just delete the folder {destination} if you have no more use of those files. Please note also that any changes committed to the extracted files will not be written back to the archive file {source}." : "Soubor s archivem {source} byl rozbalen do {destination}. Obsah archivu se zde nachází coby běžné soubory. Použijte je dle libosti. Není automatický úklid, takže rozbalené soubory zůstanou v prostoru vašich souborů, dokud je ručně nesmažete. Pokud už pro tyto soubory nemáte využití, stačí jen složku {destination} smazat. Mějte také na paměti, že jakékoli změny provedené v rozbalených souborech nebudou zapsány zpět do souboru s archivem {source}.",
    "Mounting {source} at {destination} has failed: {message}" : "Připojování {source} do {destination} se nezdařilo: {message}",
    "Extracting {source} to {destination} has failed: {message}" : "Rozbalování {source} do {destination} se nezdařilo: {message}",
    "Mounting {source} at {destination} has failed." : "Připojování {source} do {destination} se nezdařilo.",
    "Extacting {source} to {destination} bas failed." : "Rozbalování {source} do {destination} se nezdařilo.",
    "Unsupported subject: \"%s\"." : "Nepodporovaný předmět: „%s.",
    "Archive Explorer" : "Průzkumník archivu",
    "Unable to detect the archive format of \"%1$s\"." : "Nebylo možné zjistit formát archivu „%1$s“.",
    "Archive format of \"%1$s\" detected as \"%2$s\", but there is no backend driver installed which can decompress \".%3$s\" files." : "Formát archivu„%1$s“ rozpoznán jako „%2$s“, ale není přítomen žádný ovladač podpůrné vrstvy, který by dokázal rozbalit soubory „.%3$s“.",
    "Unable to deal with tar-files. Please check the installation of the app." : "Nebylo možné nějak naložit s tar soubory. Zkontrolujte instalaci aplikace.",
    "The archive format of \"%1$s\" has been detected as \"%2$s\", but there is no backend driver installed which can deal with this format." : "Formát archivu „%1$s“ byl rozpoznán jako „%2$s“, ale není nainstalován žádný ovladač podpůrné vrstvy, který by dokázal zacházet s tímto formátem.",
    "The \"%1$s\" driver could handle this format, but it is not installed." : "Ovladač „%1$s“ by tento formát mohl obsloužit, ale není nainstalován.",
    "Installation instructions: " : "Pokyny k instalaci:",
    "The \"%1$s\" driver claims to handle this format, but cannot extract the archive content." : "Ovladač „%1$s“ tvrdí že tento formát zvládá, ale nepodařilo se s ním vyzískat obsah archivu.",
    "Unable to open archive file %s (%s)" : "Nedaří se otevřít soubor s archivem %s (%s)",
    "Uncompressed size of archive \"%1$s\" is too large: %2$s > %3$s" : "Velikost archivu po rozbalení „%1$s“ je přílišná: %2$s > %3$s",
    "There is no archive file associated with this archiver instance." : "Tomuto (typu) souboru s archivem není přiřazena žádná instance archivátoru.",
    "Installation problem; the required resource \"%1$s\" of type \"%2$s\" is not installed on the server, please contact the system administrator!" : "Problém s instalací: potřebný prostředek „%1$s“ typu „%2$s“ není na serveru nainstalovaný – obraťte se na jeho správce!",
    "User" : "Uživatel",
    "Password" : "Heslo",
    "Login succeeded." : "Přihlášení úspěšné.",
    "Login failed." : "Přihlášení neúpěšné.",
    "Error, caught an exception." : "Chyba: zachycena výjimka",
    "Caused by previous exception" : "Způsobeno předchozí výjimkou",
    "bytes" : "bajtů",
    "The supplied color-string \"%s\" seems to be invalid." : "Zadaný řetězec barvy „%s“ se nezdá být platný.",
    "The input color values are invalid." : "Zadané barevné hodnoty nejsou platné.",
    "On-the-fly archive inspector for Nextcloud" : "Nástroj pro průběžnou inspekci archivů pro Nextcloud",
    "Inspect, mount and extract archive files (zip, tar, etc.)" : "Provádějte inspekci, připojujte a rozbalujte soubory s archivy (zip, tar, atd.)",
    "pick a color" : "zvolte barvu",
    "open" : "otevřít",
    "submit" : "odeslat",
    "revert color" : "vrátit barvu nazpět",
    "restore palette" : "obnovit paletu",
    "factory reset palette" : "vrátit paletu do výchozího stavu",
    "Custom Color" : "Uživatelsky určená barva",
    "Provided data is not a valid SVG image: \"{data}\"." : "Poskytnutá data není platný SVG obrázek: „{data}“.",
    "Choose a folder" : "Zvolit složku",
    "Choose a prefix-folder" : "Zvolte složku-předponu",
    "Invalid path selected: \"{dir}\"." : "Vybrán neplatný popis umístění: „{dir}.",
    "Selected path: \"{dir}/{base}/\"." : "Vyberte popis umístění: „{dir}/{base}/.",
    "Please select an item!" : "Vyberte položku!",
    "An empty value is not allowed, please make your choice!" : "Prázdná hodnota není dovolena – rozhodněte se!",
    "Click to submit your changes." : "Kliknutím odešlete své změny.",
    "Reset Changes" : "Vrátit změny",
    "Clear Selection" : "Vyčistit  výběr",
    "Unable to query the archive-format support matrix." : "Nedaří se dotázat matice podpory formátů archivů.",
    "Unable to query the information about the available archive backend drivers." : "Nepodařilo se dotázat na informaci ohledně dostupných ovladačů podpůrné vrstvy pro archivy.",
    "Archive Manager, Admin Settings" : "Správa archivů – nastavení pro správu",
    "Archive Extraction" : "Rozbalení archivu",
    "Archive Size Limit" : "Limit velikosti archivu",
    "Disallow archive extraction for archives with decompressed size larger than this limit." : "Neumožnit rozbalení archivů, u kterých by velikost po rozbalení přesáhla tento limit.",
    "Diagnostics" : "Diagnostika",
    "Archive Formats" : "Formáty archivů",
    "Supported Drivers" : "Podporované ovladače",
    "Archive Manager, Personal Settings" : "Správa archivu: Osobní nastavení",
    "Security Options" : "Předvolby zabezpečení",
    "Administrative size limit: {value}" : "Administrativní limit velikosti: {value}",
    "Mount Options" : "Předvolby pro připojování",
    "Template for the default name of the mount point" : "Šablona pro výchozí název přípojného bodu",
    "{archiveFileName} will be replaced by the filename of the archive file without extensions." : "{archiveFileName} bude nahrazeno názvem souboru (bez přípon).",
    "strip common path prefix by default" : "vždy odstraňovat běžné předpony popisů umístění",
    "automatically change the mount point name if it already exists" : "pokud už takový existuje, automaticky změnit název přípojného bodu",
    "default to scheduling mount requests as background job" : "požadavky na připojení (mount) ve výchozím stavu odbavovat naplánování úlohy na pozadí",
    "Extraction Options" : "Předvolby pro rozbalování",
    "Template for the default name of the extraction folder" : "Šablona pro výchozí název složky pro rozbalení",
    "automatically change the target folder name if the target folder already exists" : "pokud už takový existuje, automaticky změnit název cílové složky",
    "default to scheduling extraction requests as background job" : "požadavky na rozbalení ve výchozím stavu odbavovat naplánování úlohy na pozadí",
    "ok" : "OK",
    "zip bomb" : "Zip bomba",
    "too large" : "příliš velké",
    "unknown" : "neznámý",
    "Cancelling the background job failed with error {status}, \"{statusText}\"." : "Rušení úkolu na pozadí se nezdařilo s chybou {status}, „{statusText}“.",
    "Mount request failed with error {status}, \"{statusText}\"." : "Požadavek na připojení (mount) se nezdařil s chybou {status}, „{statusText}.",
    "Unmount request failed with error {status}, \"{statusText}\"." : "Požadavek na odpojení (unmout) se nezdařil s chybou {status}, „{statusText}.",
    "Archive extraction failed with error {status}, \"{statusText}\"." : "Rozbalení archivu se nezdařilo s chybou {status}, „{statusText}“.",
    "Patching the passphrase failed with error {status}, \"{statusText}\"." : "Upravování heslové fráze se nezdařilo s chybou {status}, „{statusText}“.",
    "Archive Information" : "Informace o archivu",
    "archive status" : "stav archivu",
    "archive format" : "formát archivu",
    "MIME type" : "MIME typ",
    "backend driver" : "ovladač podpůrné vrstvy",
    "uncompressed size" : "velikost po rozbalení",
    "compressed size" : "velikost s kompresí",
    "archive file size" : "velikost souboru s archivem",
    "# archive members" : "počet členů archivu",
    "common prefix" : "společná předpona",
    "creator's comment" : "poznámka tvůrce",
    "Passphrase" : "Heslová fráze",
    "unset" : "zrušit nastavení",
    "archive passphrase" : "heslová fráze k archivu",
    "Show Passphrase" : "Zobrazit heslovou frázi",
    "Mount Points" : "Přípojné body",
    "not mounted" : "nepřipojeno",
    "Disconnect storage" : "Odpojit úložiště",
    "Common prefix {prefix} is stripped." : "Společná předpona {prefix} je odstraněna.",
    "Not mounted, create a new mount point:" : "Nepřipojeno, vytvořit nový přípojný bod:",
    "base name" : "základ názvu",
    "strip common path prefix" : "oříznout společnou předponu popisu umístění",
    "schedule as background job" : "naplánovat jako úlohu na pozadí",
    "Extract Archive" : "Rozbalit archiv",
    "Choose a directory to extract the archive to:" : "Zvolte složku, do které archiv rozbalit:",
    "basename" : "základ názvu",
    "Pending Background Jobs" : "Čekající úlohy na pozadí",
    "No Pending Background Jobs" : "Žádné čekající úlohy na pozadí",
    "Cancel Job" : "Zrušit úlohu",
    "mount" : "připojit",
    "extract" : "Rozbalit",
    "Common prefix {prefix} will be stripped." : "Společná předpona {prefix} bude odstraněna.",
    "No pending background job." : "Žádné čekající úlohy na pozadí.",
    "Job type: {type}" : "Typ úlohy: {type}",
    "Mount Archive" : "Připojit archiv",
    "The archive \"{archivePath}\" is already mounted on \"{mountPointPath}\"." : "Archiv „{archivePath}“ už je připojený v „{mountPointPath}.",
    "The archive \"{archivePath}\" has been mounted on \"{mountPointPath}\"." : "Soubor „{archivePath}“ bylo připojeno v „{mountPointPath}“.",
    "Unable to obtain mount status for archive file \"{archivePath}\"." : "Nedaří se získat stav připojení souboru s archivem „{archivePath}“.",
    "Archive" : "Archiv",
    "OK" : "OK",
    "Created" : "Vytvořeno",
    "Accepted" : "Přijato",
    "Non-Authoritative Information" : "Neautoritativní informace",
    "No Content" : "Žádný obsah",
    "Reset Content" : "Resetovat obsah",
    "Partial Content" : "Částečný obsah",
    "Multi-Status (WebDAV)" : "Více stavů (WebDAV)",
    "Already Reported (WebDAV)" : "Už nahlášeno (WebDAV)",
    "IM Used" : "IM použito",
    "Multiple Choices" : "Vícero voleb",
    "Moved Permanently" : "Natrvalo přesunuto",
    "Found" : "Nalezeno",
    "See Other" : "Viz ostatní",
    "Not Modified" : "Nezměněno",
    "Use Proxy" : "Použít proxy",
    "(Unused)" : "(Nepoužito)",
    "Temporary Redirect" : "Dočasné přesměrování",
    "Permanent Redirect (experimental)" : "Trvalé přesměrování (experimentální)",
    "Bad Request" : "Chybný požadavek",
    "Unauthorized" : "Není pověření",
    "Payment Required" : "Je vyžadována platba",
    "Forbidden" : "Zakázáno",
    "Not Found" : "Nenalezeno",
    "Method Not Allowed" : "Metoda nedovolena",
    "Not Acceptable" : "Nepřijatelné",
    "Proxy Authentication Required" : "Proxy vyžaduje ověření se",
    "Request Timeout" : "Překročen časový limit požadavku",
    "Conflict" : "Konflikt",
    "Gone" : "Pryč",
    "Length Required" : "Je vyžadována délka",
    "Precondition Failed" : "Předběžná podmínka nesplněna",
    "Request Entity Too Large" : "Příliš velká entita požadavku",
    "Request-URI Too Long" : "Příliš dlouhá URI požadavku",
    "Unsupported Media Type" : "Nepodporovaný typ média",
    "Requested Range Not Satisfiable" : "Požadovaný rozsah nelze splnit",
    "Expectation Failed" : "Očekávání nenaplněno",
    "I'm a teapot (RFC 2324)" : "Jsem konvice na čaj (RFC 2324)",
    "Enhance Your Calm (Twitter)" : "Rozviňte svůj klid (Twitter)",
    "Unprocessable Entity (WebDAV)" : "Nezpracovatelná entita (WebDAV)",
    "Locked (WebDAV)" : "Uzamčeno (WebDAV)",
    "Failed Dependency (WebDAV)" : "Nezdařená závislost (WebDAV)",
    "Reserved for WebDAV" : "Vyhrazeno pro WebDAV",
    "Upgrade Required" : "Je zapotřebí přejít na novější verzi",
    "Precondition Required" : "Nutné splnění předběžné podmínky",
    "Too Many Requests" : "Příliš mnoho požadavků",
    "Request Header Fields Too Large" : "Příliš dlouhé kolonky hlavičky požadavku",
    "No Response (Nginx)" : "Bez odpovědi (Nginx)",
    "Retry With (Microsoft)" : "Zkusit znovu s (Microsoft)",
    "Blocked by Windows Parental Controls (Microsoft)" : "Zablokováno Rodičovskou ochranou (Microsoft)",
    "Unavailable For Legal Reasons" : "Není k dispozici z právních důvodů",
    "Client Closed Request (Nginx)" : "Klient zavřel požadavek (Nginx)",
    "Internal Server Error" : "Vnitřní chyba serveru",
    "Not Implemented" : "Neimplementováno",
    "Bad Gateway" : "Chybná brána",
    "Service Unavailable" : "Služba nedostupn",
    "Gateway Timeout" : "Časový limit brány",
    "HTTP Version Not Supported" : "Nepodporovaná verze HTTP protokolu",
    "Variant Also Negotiates (Experimental)" : "Varianta také dojednává (experimentální)",
    "Insufficient Storage (WebDAV)" : "Nedostatečné úložiště (WebDAV)",
    "Loop Detected (WebDAV)" : "Zjištěna smyčka (WebDAV)",
    "Bandwidth Limit Exceeded (Apache)" : "Přesažen limit přenosové rychlosti (Apache)",
    "Not Extended" : "Nerozšířeno",
    "Network Authentication Required" : "Je vyžadováno ověření sítě",
    "Network read timeout error" : "Chyba – překročen časový limit čekání na načtení ze sítě",
    "Network connect timeout error" : "Chyba – překročen časový limit čekání připojení k síti",
    "Operation cancelled by user." : "Operace zrušena uživatelem.",
    "Aborted" : "Přerušeno",
    "Error" : "Chyba",
    "System Administrator" : "Správce systému",
    "Feedback email: {AutoReport}" : "E-mail se zpětnou vazboul: {AutoReport}",
    "Something went wrong." : "Něco se pokazilo.",
    "If it should be the case that you are already logged in for a long time without interacting with the app, then the reason for this error is probably a simple timeout." : "Pokud by se jednalo o případ, že jste už dlouho přihlášení a přitom v aplikaci nic neděláte, pak důvodem pro tuto chybu je nejspíš jednoduše překročení časového limitu nečinnosti.",
    "In any case it may help to logoff and logon again, as a temporary workaround. You will be redirected to the login page when you close this window." : "V každém případě může pomoci se odhlásit a znovu přihlásit – coby dočasné obejití problému. Poté, co toto okno zavřete, budete přesměrováni na přihlašovací obrazovku.",
    "Unrecoverable unknown internal error, no details available" : "Neznámá vnitřní chyba, kvůli které nelze dále pokračovat. Podrobnosti nejsou k dispozici",
    "Internal Error" : "Vnitřní chyba",
    "Field {RequiredField} not present in AJAX response." : "Kolonka {RequiredField} nepřítomná v AJAX odpovědi.",
    "Missing data" : "Schází údaje",
    "The submitted data \"{stringValue}\" seems to be a plain string, but we need an object where the data is provided through above listed properties." : "Poskytnutá data „{stringValue}“ se zdají být holým řetězcem, jenže je zapotřebí objektu, ve kterém jsou data poskytnuta prostřednictvím výše uvedených vlastností.",
    "Error: plain string received" : "Chyba: obdržen holý řetězec",
    "The submitted data is not a plain object, and does not provide the properties listed above." : "Odeslaná data nejsou holým objektem a neposkytují výše vypsané vlastnosti.",
    "Error: not a plain object" : "Chyba: nejedná se o holý objekt",
    "Unknown JSON error response to AJAX call: {status} / {error}" : "Neznámá JSON chybová odpověď na AJAX volání: {status} / {error}",
    "HTTP error response to AJAX call: {code} / {error}" : "HTTP chybová odpověď na AJAX volání: {code} / {error}",
    "Yes" : "Ano",
    "No" : "Ne",
    "Debug Information" : "Informace pro ladění",
    "The quick brown fox jumps over the lazy dog." : "Příliš žluťoučký kůň úpěl ďábelské ódy.",
    "reason unknown" : "příčina neznámá",
    "Unable to query the initial value of all settings: {message}" : "Nedaří se dotazovat úvodní hodnotu všech nastavení: {message}",
    "Unable to query the initial value of {settingsKey}: {message}" : "Nedaří se dotázat na úvodní hodnotu {settingsKey}: {message}",
    "true" : "pravda",
    "Successfully set \"{settingsKey}\" to {value}." : "Úspěšně nastaveno „{settingsKey}“ na {value}.",
    "Setting \"{settingsKey}\" has been unset successfully." : "Nastavení „{settingsKey}“ bylo úspěšně zrušeno.",
    "Unable to set \"{settingsKey}\" to {value}: {message}." : "Nedaří se nastavit „{settingsKey}“ na {value}: {message}.",
    "Unable to unset \"{settingsKey}\": {message}" : "Nedaří se zrušit nastavení „{settingsKey}: {message}",
    "Confirmation Required" : "Je vyžadováno potvrzení",
    "Unconfirmed, reverting to old value." : "Nepotvrzeno – vrací se na původní hodnotu.",
    "Successfully set value for \"{settingsKey}\" to \"{displayValue}\"" : "Úspěšně nastavena hodnota pro „{settingsKey}“ na „{displayValue}",
    "Setting \"{setting}\" has been unset successfully." : "Nastavení „{setting}“ bylo úspěšně zrušeno.",
    "Could not set value for \"{settingsKey}\" to \"{value}\": {message}" : "Nedaří se nastavit hodnotu pro „{settingsKey}“ na „{value}“: {message}",
    "Failed to mount archive file \"{archivePath}: {message}\"." : "Nepodařilo se připojit soubor archivu „{archivePath}: {message}“",
    "Failed to mount archive file \"{archivePath}\"." : "Nepodařilo se připojit soubor archivu „{archivePath}“",
    "Unable to obtain mount status for archive file \"{archivePath}: {message}\"." : "Nedaří se získat stav připojení souboru s archivem „{archivePath}: {message}“.",
    "false" : "nepravda"
},
"nplurals=4; plural=(n == 1 && n % 1 == 0) ? 0 : (n >= 2 && n <= 4 && n % 1 == 0) ? 1: (n % 1 != 0 ) ? 2 : 3;");
