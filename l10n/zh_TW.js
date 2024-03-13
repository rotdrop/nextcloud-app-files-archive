OC.L10N.register(
    "files_archive",
    {
    "File or folder could not be found." : "找不到檔案或資料夾。",
    "The user folder for user \"%s\" could not be opened." : "無法開啟使用者「%s」的使用者資料夾。",
    "The archive file \"%s\" could not be found on the server." : "無法在伺服氣上找到封存檔「%s」。",
    "Unable to open the archive file \"%s\": %s." : "無法開啟封存檔「%s」：%s。",
    "Error: %s" : "錯誤：%s",
    "Unable to open the archive file \"%s\"." : "無法開啟封存檔「%s」。",
    "The archive file \"%1$s\" appears to be a zip-bomb: uncompressed size %2$s > admin limit %3$s." : "封存檔案「%1$s」似乎是 zip 炸彈：未壓縮大小 %2$s > 管理員限制 %3$s。",
    "The archive file \"%1$s\" is too large: uncompressed size %2$s > user limit %3$s." : "封存檔「%1$s」太大了：未壓縮大小 %2$s > 使用者限制 %3$s。",
    "Unable to open the target parent folder \"%s\"." : "無法開啟目標上層資料夾「%s」。",
    "The target folder \"%s\" already exists and auto-rename is not enabled." : "目標資料夾「%s」已存在且未啟用自動重新命名。",
    "Unable to extract \"%1$s\" to \"%2$s\": \"%3$s\"." : "無法解壓縮「%1$s」到「%2$s」：「%3$s」。",
    "Extracting \"%1$s\" to \"%2$s\" succeeded." : "解壓縮「%1$s」到「%2$s」成功。",
    "Archive background mount job scheduled successfully." : "成功排程壓縮檔背景掛載作業。",
    "Archive background extraction job scheduled successfully." : "成功排程壓縮檔背景解壓縮作業。",
    "Post to endpoint \"%s\" not implemented." : "未實作到端點「%s」的 POST。",
    "Post to base URL of app \"%s\" not allowed." : "不允許到應用程式「%s」基礎 URL 的 POST。",
    "Get from endpoint \"%s\" not implemented." : "從端點「%s」的 GET 未實作。",
    "\"%1$s\" is already mounted on \"%2$s\"." : "「%1$s」已掛在於「%2$s」。",
    "Unable to open parent folder \"%1$s\" of mount point \"%2$s\": %3$s." : "無法開啟掛載點「%2$s」的上層資料夾「%1$s」：%3$s。",
    "The mount point \"%s\" already exists and auto-rename is not enabled." : "掛載點「%s」已存在且未啟用自動重新命名。",
    "Unable to update the file cache for the mount point \"%1s\": %2$s." : "無法更新掛載點「%1s」的檔案快取：%2$s。",
    "\"%s\" is not mounted." : "「%s」未掛載。",
    "Directory \"%s\" is not a mount point." : "目錄「%s」不是掛載點。",
    "Archive \"%1$s\" has been unmounted from \"%2$s\"." : "封存檔「%1$s」已從「%2$s」取消掛載。",
    "Only the passphrase may be changed for an existing mount." : "僅能變更既有掛載的密碼。",
    "Unknown admin setting: \"%1$s\"" : "未知的管理設定：「%1$s」",
    "The admin setting \"%1$s\" is read-only" : "管理設定「%1$s」是唯讀的",
    "Unknown personal setting: \"%1$s\"" : "未知的個人設定：「%1$s」",
    "The personal setting \"%1$s\" is read-only" : "個人設定「%1$s」是唯讀的",
    "Value \"%1$s\" for setting \"%2$s\" is not convertible to boolean." : "設定「%2$s」的值「%1$s」無法轉換為布林值。",
    "The target folder template \"%1$s\" must contain the archive file placeholder \"%2$s\"." : "目標資料夾範本「%1$s」必須包含封存檔佔位字串「%2$s」。",
    "Unknown personal setting: \"%s\"." : "未知的個人設定：「%s」。",
    "Unable to parse memory size limit \"%s\"" : "無法解析記憶體大小限制：「%s」",
    "Archive Manager" : "封存檔管理程式",
    "The archive file {source} will be mounted as a virtual folder at {destination}." : "封存檔案 {source} 將會作為虛擬資料夾掛載在 {destination}。",
    "The archive file {source} will be extracted to {destination}." : "封存檔案 {source} 將會解壓縮至 {destination}。",
    "Archive mount of {source} succeeded." : "封存掛載 {source} 成功。",
    "Archive extraction of {source} succeeded." : "封存解壓縮 {source} 成功。",
    "Your archive file {source} has been mounted as a virtual folder at {destination}. In order to unmount it it is safe to just delete the mount point {destination}. This will do no harm to the archive file and just issue an \"unmount\" action. Please note that the virtual folder is read-only." : "您的封存檔案 {source} 已於 {destination} 掛載為虛擬資料夾。若要解除掛載它，只要刪除掛載點 {destination} 即可。這不會對封存檔案造成任何損害，只會執行「解除掛載」動作。請注意，虛擬資料夾是唯讀的。",
    "Your archive file {source} has been extracted to {destination}. The archive contents reside there as ordinary files. Please feel free to use those as it pleases you. There is not automatic cleanup, the extracted files will remain in your file-space until you delete them manually. Just delete the folder {destination} if you have no more use of those files. Please note also that any changes committed to the extracted files will not be written back to the archive file {source}." : "您的封存檔案 {source} 已解壓縮至 {destination}。封存檔案內容以普通檔案形式存在那裡。請隨意使用那些檔案。不會自動清除，解壓縮的檔案將會保留在您的檔案空間中，直到您手動刪除它們。若您不再需要那些檔案，只要刪除資料夾 {destination} 即可。請注意，對解壓縮的檔案所作的任何變更將不會寫入回封存檔案 {source}。",
    "Mounting {source} at {destination} has failed: {message}" : "掛載 {source} 於 {destination} 失敗：{message}",
    "Extracting {source} to {destination} has failed: {message}" : "解壓縮 {source} 至 {destination} 失敗：{message}",
    "Mounting {source} at {destination} has failed." : "掛載 {source} 於 {destination} 失敗。",
    "Extacting {source} to {destination} bas failed." : "解壓縮 {source} 至 {destination} 失敗。",
    "Unsupported subject: \"%s\"." : "不支援的主體：「%s」。",
    "Archive Explorer" : "封存檔瀏覽程式",
    "Unable to open archive file %s (%s)" : "無法開啟封存檔 %s (%s)",
    "Uncompressed size of archive \"%1$s\" is too large: %2$s > %3$s" : "封存檔「%1$s」的未壓縮大小太大了：%2$s > %3$s",
    "There is no archive file associated with this archiver instance." : "沒有與此封存程式實體相關的封存檔。",
    "Installation problem; the required resource \"%1$s\" of type \"%2$s\" is not installed on the server, please contact the system administrator!" : "安裝問題；伺服器上沒有安裝必要的類型為「%2$s」的資源「%1$s」，請聯絡系統管理員！",
    "User" : "使用者",
    "Password" : "密碼",
    "Login succeeded." : "登入成功。",
    "Login failed." : "登入失敗。",
    "Error, caught an exception." : "錯誤，捕捉到例外",
    "Caused by previous exception" : "由先前的例外造成",
    "bytes" : "位元組",
    "The supplied color-string \"%s\" seems to be invalid." : "提供的顏色字串「%s」似乎是無效的。",
    "The input color values are invalid." : "輸入的顏色值無效。",
    "On-the-fly archive inspector for Nextcloud" : "Nextcloud 的即時封存檔檢查程式",
    "Inspect, mount and extract archive files (zip, tar, etc.)" : "檢查、掛載與解壓縮封存檔（zip、tar 等等）",
    "pick a color" : "挑選顏色",
    "open" : "開啟",
    "submit" : "submit",
    "restore palette" : "還原調色盤",
    "factory reset palette" : "將調色盤重設為出廠預設值",
    "Choose a folder" : "選擇資料夾",
    "Choose a prefix-folder" : "選擇前綴資料夾",
    "Invalid path selected: \"{dir}\"." : "選取了無效路徑：「{dir}」。",
    "Selected path: \"{dir}/{base}/\"." : "已選取的路徑：「{dir}/{base}/」。",
    "Archive Manager, Admin Settings" : "封存檔管理程式，管理員設定",
    "Archive Extraction" : "封存檔案解壓縮",
    "Archive Size Limit" : "封存檔大小限制",
    "Disallow archive extraction for archives with decompressed size larger than this limit." : "不允許對未壓縮大小大於此限制的檔案進行檔案解壓縮。",
    "Archive Manager, Personal Settings" : "封存檔管理程式，個人設定",
    "Security Options" : "安全性選項",
    "Administrative size limit: {value}" : "管理大小限制：{value}",
    "Mount Options" : "掛載選項",
    "Template for the default name of the mount point" : "掛載點的預設名稱範本",
    "{archiveFileName} will be replaced by the filename of the archive file without extensions." : "{archiveFileName} 將取代為封存檔的檔案名稱，不包含副檔名。",
    "strip common path prefix by default" : "預設去除共同路徑前綴",
    "automatically change the mount point name if it already exists" : "若其已存在，則自動變更掛載點名稱",
    "default to scheduling mount requests as background job" : "預設將掛載請求排程為背景作業。",
    "Extraction Options" : "解壓縮選項",
    "Template for the default name of the extraction folder" : "解壓縮資料夾的預設名稱範本",
    "automatically change the target folder name if the target folder already exists" : "若目標資料夾已存在，則自動變更目標資料夾名稱",
    "default to scheduling extraction requests as background job" : "預設將解壓縮請求排程為背景作業。",
    "Archive Information" : "封存檔資訊",
    "archive status" : "封存檔狀態",
    "archive format" : "封存檔格式",
    "unknown" : "未知",
    "MIME type" : "MIME 類型",
    "backend driver" : "後端驅動程式",
    "uncompressed size" : "未壓縮大小",
    "compressed size" : "壓縮大小",
    "archive file size" : "封存檔案大小",
    "# archive members" : "封存檔成員個數",
    "common prefix" : "共同前綴",
    "creator's comment" : "建立者的留言",
    "Passphrase" : "通關密語",
    "unset" : "未設定",
    "archive passphrase" : "封存檔通關密語",
    "Show Passphrase" : "顯示通關密語",
    "Mount Points" : "掛載點",
    "not mounted" : "未掛載",
    "Common prefix {prefix} is stripped." : "共同前綴 {prefix} 被去除。",
    "Not mounted, create a new mount point:" : "未掛載，建立新掛載點：",
    "base name" : "基礎名稱",
    "strip common path prefix" : "去除共同路徑前綴",
    "schedule as background job" : "排程為背景作業",
    "Extract Archive" : "解壓縮封存檔",
    "Choose a directory to extract the archive to:" : "選擇要解壓縮封存檔到哪個目錄：",
    "basename" : "基礎名稱",
    "ok" : "確定",
    "zip bomb" : "zip 炸彈",
    "too large" : "太大",
    "Mount request failed with error {status}, \"{statusText}\"." : "掛載請求失敗，錯誤 {status}，「{statusText}」。",
    "Unmount request failed with error {status}, \"{statusText}\"." : "解除掛載請求失敗，錯誤 {status}，「{statusText}」。",
    "Archive extraction failed with error {status}, \"{statusText}\"." : "封存檔解壓縮失敗，錯誤 {status}，「{statusText}」。",
    "Patching the passphrase failed with error {status}, \"{statusText}\"." : "修補通關密語失敗，錯誤 {status}，「{statusText}」。",
    "Mount Archive" : "掛載封存檔",
    "Unable to obtain mount status for archive file \"{archivePath}\"." : "無法擷取封存檔「{archivePath}」的掛載狀態。",
    "The archive \"{archivePath}\" is already mounted on \"{mountPointPath}\"." : "封存檔「{archivePath}」已掛載於「{mountPointPath}」。",
    "Archive" : "壓縮檔",
    "The archive \"{archivePath}\" has been mounted on \"{mountPointPath}\"." : "封存檔「{archivePath}」已掛載於「{mountPointPath}」。",
    "Failed to mount archive file \"{archivePath}: {message}\"." : "掛載封存檔案「{archivePath}：{message}」失敗。",
    "Failed to mount archive file \"{archivePath}\"." : "掛載封存檔案「{archivePath}」失敗。",
    "Unable to obtain mount status for archive file \"{archivePath}: {message}\"." : "無法擷取封存檔「{archivePath}：{message}」的掛載狀態。",
    "reason unknown" : "未知理由",
    "Unable to query the initial value of all settings: {message}" : "無法查詢所有設定的初始值：{message}",
    "Unable to query the initial value of {settingsKey}: {message}" : "無法查詢 {settingsKey} 的初始值：{message}",
    "true" : "true",
    "Successfully set \"{settingsKey}\" to {value}." : "成功將「{settingsKey}」設定為 {value}.",
    "Setting \"{settingsKey}\" has been unset successfully." : "設定「{settingsKey}」已成功取消設定。",
    "Unable to set \"{settingsKey}\" to {value}: {message}." : "無法設定「{settingsKey}」為 {value}：{message}。",
    "false" : "false",
    "Unable to unset \"{settingsKey}\": {message}" : "無法取消設定「{settingsKey}」：{message}",
    "Confirmation Required" : "需要確認",
    "Unconfirmed, reverting to old value." : "未確認，還原至舊值。",
    "Successfully set value for \"{settingsKey}\" to \"{displayValue}\"" : "成功設定「{settingsKey}」的值為「{displayValue}」",
    "Setting \"{setting}\" has been unset successfully." : "設定「{setting}」已成功取消設定。",
    "Could not set value for \"{settingsKey}\" to \"{value}\": {message}" : "無法將「{settingsKey}」的值設定為「{value}」：{message}",
    "OK" : "確定",
    "Created" : "已建立",
    "Accepted" : "已接受",
    "Non-Authoritative Information" : "非權威資訊",
    "No Content" : "無內容",
    "Reset Content" : "重設內容",
    "Partial Content" : "部份內容",
    "Multi-Status (WebDAV)" : "多狀態 (WebDAV)",
    "Already Reported (WebDAV)" : "已回報 (WebDAV)",
    "IM Used" : "使用 IM",
    "Multiple Choices" : "多重選擇",
    "Moved Permanently" : "已永久移動",
    "Found" : "找到",
    "See Other" : "檢視其他",
    "Not Modified" : "未修改",
    "Use Proxy" : "使用代理伺服器",
    "(Unused)" : "（未使用）",
    "Temporary Redirect" : "暫時重新導向",
    "Permanent Redirect (experimental)" : "永久重新導向（實驗性）",
    "Bad Request" : "請求無效",
    "Unauthorized" : "未授權",
    "Payment Required" : "需要付款",
    "Forbidden" : "存取被拒",
    "Not Found" : "找不到",
    "Method Not Allowed" : "不允許的方法",
    "Not Acceptable" : "無法接受",
    "Proxy Authentication Required" : "代理伺服器要求驗證",
    "Request Timeout" : "請求逾時",
    "Conflict" : "衝突",
    "Gone" : "不可用",
    "Length Required" : "需要長度",
    "Precondition Failed" : "先決條件失敗",
    "Request Entity Too Large" : "請求的實體太大",
    "Request-URI Too Long" : "請求的 URI 太長",
    "Unsupported Media Type" : "不支援的媒體類型",
    "Requested Range Not Satisfiable" : "請求的範圍無法滿足",
    "Expectation Failed" : "預期失敗",
    "I'm a teapot (RFC 2324)" : "I'm a teapot (RFC 2324)",
    "Enhance Your Calm (Twitter)" : "Enhance Your Calm (Twitter)",
    "Unprocessable Entity (WebDAV)" : "Unprocessable Entity (WebDAV)",
    "Locked (WebDAV)" : "Locked (WebDAV)",
    "Failed Dependency (WebDAV)" : "Failed Dependency (WebDAV)",
    "Reserved for WebDAV" : "Reserved for WebDAV",
    "Upgrade Required" : "Upgrade Required",
    "Precondition Required" : "Precondition Required",
    "Too Many Requests" : "Too Many Requests",
    "Request Header Fields Too Large" : "Request Header Fields Too Large",
    "No Response (Nginx)" : "No Response (Nginx)",
    "Retry With (Microsoft)" : "Retry With (Microsoft)",
    "Blocked by Windows Parental Controls (Microsoft)" : "Blocked by Windows Parental Controls (Microsoft)",
    "Unavailable For Legal Reasons" : "Unavailable For Legal Reasons",
    "Client Closed Request (Nginx)" : "Client Closed Request (Nginx)",
    "Internal Server Error" : "內部伺服器錯誤",
    "Not Implemented" : "Not Implemented",
    "Bad Gateway" : "Bad Gateway",
    "Service Unavailable" : "Service Unavailable",
    "Gateway Timeout" : "Gateway Timeout",
    "HTTP Version Not Supported" : "HTTP Version Not Supported",
    "Variant Also Negotiates (Experimental)" : "Variant Also Negotiates (Experimental)",
    "Insufficient Storage (WebDAV)" : "Insufficient Storage (WebDAV)",
    "Loop Detected (WebDAV)" : "Loop Detected (WebDAV)",
    "Bandwidth Limit Exceeded (Apache)" : "Bandwidth Limit Exceeded (Apache)",
    "Not Extended" : "Not Extended",
    "Network Authentication Required" : "Network Authentication Required",
    "Network read timeout error" : "Network read timeout error",
    "Network connect timeout error" : "Network connect timeout error",
    "Operation cancelled by user." : "操作被使用者取消。",
    "Aborted" : "已中止",
    "Error" : "錯誤",
    "System Administrator" : "系統管理員",
    "Feedback email: {AutoReport}" : "回饋電子郵件：{AutoReport}",
    "Something went wrong." : "出了點問題。",
    "If it should be the case that you are already logged in for a long time without interacting with the app, then the reason for this error is probably a simple timeout." : "若應該是您已經登入很長一段時間但沒有與應用程式互動的情況，那麼這個錯誤的原因可能只是逾時。",
    "In any case it may help to logoff and logon again, as a temporary workaround. You will be redirected to the login page when you close this window." : "無論如何，作為臨時的解決方案，登出再登入可能會有所幫助。當您關閉此視窗時，您會被重新導向至登入頁面。",
    "Unrecoverable unknown internal error, no details available" : "無法還原的未知內部錯誤，無可用的詳細資訊",
    "Internal Error" : "內部錯誤",
    "Field {RequiredField} not present in AJAX response." : "AJAX 回應中沒有欄位 {RequiredField}。",
    "Missing data" : "資料遺失",
    "The submitted data \"{stringValue}\" seems to be a plain string, but we need an object where the data is provided through above listed properties." : "遞交的資料「{stringValue}」似乎是純文字字串，但我們需要一個透過上面列出的屬性提供資料的物件。",
    "Error: plain string received" : "錯誤：收到純文字字串",
    "The submitted data is not a plain object, and does not provide the properties listed above." : "提交的資料不是普通物件，不提供上面列出的屬性。",
    "Error: not a plain object" : "錯誤：非普通物件",
    "Unknown JSON error response to AJAX call: {status} / {error}" : "對 AJAX 呼叫的未知 JSON 錯誤回應：{status} / {error}",
    "HTTP error response to AJAX call: {code} / {error}" : "對 AJAX 呼叫的 HTTP 錯誤回應：{code} / {error}",
    "No" : "否",
    "Yes" : "是",
    "Debug Information" : "除錯資訊",
    "Your archive file {source} has been mounted as a virtual folder at {destination}." : "封存檔案 {source} 已作為虛擬資料夾掛載在 {destination}。",
    "Your archive file {source} has been extracted to {destination}." : "您的封存檔案 {source} 已解壓縮至 {destination}。",
    "Choose a prefix folder" : "選擇前綴資料夾"
},
"nplurals=1; plural=0;");
