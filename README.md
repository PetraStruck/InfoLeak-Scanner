# InfoLeak-Scanner

## Docker
First run `docker run --rm -p 8000:80 siwecos/infoleak-scanner`.

Open your browser and use the scanner: `http://localhost/?url=<URL>`

## Install

Get php5, curl and a webserver:
```
sudo apt-get install apache2 php5 php5-curl
```

Copy application into webserver:
```
cp -R . /var/www/html/
```

## What?

### Content-Management-System (CMS)
- Drupal
- Joomla
- vbulletin
- Veyton
- Webspell
- Wordpress
- xt-commerce

### Plugins
Searches plugins for the detected CMS. Biggest list (wordpress) contains 980 different plugins.

### JavaScript libraries
Searches for vulnerable and most used JavaScript libraries.

### Mail adresses
Searches for mail adresses. Interesting for spam and/or social engineering attacks.

### Phone numbers
Searches for phone numbers. Interesting for social engineering attacks and/or scam.


## Score
```
| Finding                         | Score (0-100) |
|---------------------------------+---------------|
| Content-Management-System (CMS) |            60 |
| Plugins                         |            70 |
| JavaScript libraries            |            50 |
| Mails adresses                  |            70 |
| Phone numbers                   |            40 |
```

Scores are set using:

[Vulnerability Scoring System Version 3.0 Calculator](https://www.first.org/cvss/)

[JavaScript](https://www.sourceclear.com/registry/security/information-disclosure/javascript/sid-2371/risk)

[E-Mail](http://www.huawei.com/en-CA/psirt/security-advisories/2016/huawei-sa-20161214-01-smartphone-en)


## Running the scanner
You can run the scanner via POST and GET requests.

### POST
If you want to run the scanner with a POST request you have to send the
parameters in a JSON encoded format:
``` 
{
  "url": "string",
  "dangerLevel": 0,
  "callbackurls": [
    "string"
  ]
}
```
`url` defines the URL which should be scanned.
`dangerLevel` is not relevant, simply define it to 0.
`callbackurls` is an array of URLs. These URLs will get the result of the
scanner (sent via POST).

### GET
Running the scanner with a GET request is much simpler. All you have to do is to
run the application a given URL:

`http://localhost/?url=<URL>`


### Verbose mode
No findings in any checks:
```
{
    "checks": {
        "cms": {
            "result": false,
            "risk": 0,
            "comment": "Es konnte keine CMS detektiert werden.",
            "finding": "N/A."
        },
        "plugin": {
            "result": false,
            "risk": 0,
            "comment": "Es konnten keine Plugins gefunden werden.",
            "finding": "N/A."
        },
        "javascript": {
            "result": false,
            "risk": 0,
            "comment": "Es konnte keine JavaScript Bibliothek gefunden werden.",
            "finding": "N/A."
        },
        "email": {
            "result": false,
            "risk": 0,
            "comment": "Es konnte keine E-Mail Adresse gefunden werden.",
            "finding": "N/A."
        },
        "phone": {
            "result": false,
            "risk": 0,
            "comment": "Es konnten keine Telefonnummern gefunden werden.",
            "finding": "N/A."
        }
    }
}
```

At least one finding in any check:
```
{
    "checks": {
        "cms": {
            "result": true,
            "risk": 6,
            "comment": "Die verwendete CMS konnte ermittelt werden (wordpress).",
            "finding": "[img]: [...]"
        },
        "plugin": {
            "result": true,
            "risk": 7,
            "comment": [
                "Ein verwendetes Plugin konnte detektiert werden (contact-form-7).",
                "Ein verwendetes Plugin konnte detektiert werden (styles).",
                "Ein verwendetes Plugin konnte detektiert werden (groups)."
            ],
            "finding": [
                "[...]/contact-form-7/includes/css/styles.css?ver=4.8",
                "[...]/contact-form-7/includes/css/styles.css?ver=4.8",
                "[...]?gid=416&trk=group-name"
            ]
        },
        "javascript": {
            "result": true,
            "risk": 5,
            "comment": "Es wurde eine JavaScript Bibliothek gefunden für dessen Version eine Schwachstelle existiert (jquery 1.4.1).",
            "finding": {
                "attr": "[...]/wp-includes/js/jquery/jquery-migrate-1.4.1.js",
                "version": "1.4.1"
            }
        },
        "email": {
            "result": true,
            "risk": 7,
            "comment": "Die Offenlegung von E-Mail Adressen könnte zu ungewünschtem Spam und unter anderem auch zu einer gezielten Phishing Attacke führen.",
            "finding": "mail1@host.de, mail2@host.de, mail3@host.de"
        },
        "phone": {
            "result": true,
            "risk": 4,
            "comment": "Die Offenlegung von Telefonnummern....",
            "finding": "123456, 654321"
        }
    }
}
```

# Scanner Interface Values

## InfoLeak-Scanner

### Messages

| Placeholder                | Message                                                                                                                                   |
|----------------------------+-------------------------------------------------------------------------------------------------------------------------------------------|
| **SCAN CMS**               | 								                                                                                                          |
| CMS_ONLY                   | Used Content-Management-System {cms} detected.                                                                                            |
| CMS_VERSION                | Used Content-Management-System {cms} and its version {version} detected.                                                                  |
| CMS_VERSION_VULN           | Vulnerable Content-Management-System {cms} version {version} detected                                                                     |
| **SCAN PLUGIN**            |                                                                                                                                           |
| PLUGIN_ONLY                | CMS Plugin {plugin} in DOM-node {node} via node-content {node_content} detected.                                                          |
| PLUGIN_VERSION             | CMS Plugin {plugin} and its version {plugin_version} in DOM-node {node} via node-content {node_content} detected.                         |
| PLUGIN_VERSION_VULN        | Vulnerable CMS Plugin {plugin} and its version {plugin_version} in DOM-node {node} via node-content {node_content} detected.              |
| **SCAN JS**                |                                                                                                                                           |
| JS_LIB_ONLY                | Used JavaScript library {js_lib_name} in DOM-node {node} via node-content {node_content} detected.                                        |
| JS_LIB_VERSION             | Used JavaScript library {js_lib_name} and its version {js_lib_version} in DOM-node {node} via node-content {node_content} detected.       |
| JS_LIB_VULN_VERSION        | Vulnerable JavaScript library {js_lib_name} and its version {js_lib_version} in DOM-node {node} via node-content {node_content} detected. |
| **SCAN EMAIL**             |                                                                                                                                           |
| EMAIL_FOUND                | Email address {email_address} found.                                                                                                      |
| **SCAN PHONE**             |                                                                                                                                           |
| NUMBER_FOUND               | Telephone number {number} found.                                                                                                          |
|                            |                                                                                                                                           |
| **ERRORS**                 |                                                                                                                                           |
| NO_SOURCE_CODE             | Given URL has no source code.                                                                                                             |
| NO_VALID_URL               | Given URL is not a valid URL.                                                                                                             |
| LOCALHOST_SCAN_NOT_ALLOWED | Scanning localhost ist not permitted.                                                                                                     |
| NOT_REACHABLE              | Given URL is not reachable.                                                                                                               |
| PORT_DISALLOWED            | Given URL contains a disallowed port.                                                                                                     |
| DONT_LEAK_USER_CREDS       | Given URL contains user credentials.                                                                                                      |
| JSON_DECODE_ERROR          | Given POST request could not be decoded.                                                                                                  |



## Further details
Especially tested on:

```
	Mozilla Firefox 45.7.0

	PHP 5.6.30-0+deb8u1 (cli) (built: Feb  8 2017 08:50:21)
	Copyright (c) 1997-2016 The PHP Group
	Zend Engine v2.6.0, Copyright (c) 1998-2016 Zend Technologies

	Server version: Apache/2.4.10 (Debian)
	Server built:   Feb 24 2017 18:40:28
	Server's Module Magic Number: 20120211:37
	Server loaded:  APR 1.5.1, APR-UTIL 1.5.4
	Compiled using: APR 1.5.1, APR-UTIL 1.5.4
	Architecture:   64-bit
	Server MPM:     prefork
	threaded:     no
	forked:     yes (variable process count)
	Server compiled with....
	-D APR_HAS_SENDFILE
	-D APR_HAS_MMAP
	-D APR_HAVE_IPV6 (IPv4-mapped addresses enabled)
	-D APR_USE_SYSVSEM_SERIALIZE
	-D APR_USE_PTHREAD_SERIALIZE
	-D SINGLE_LISTEN_UNSERIALIZED_ACCEPT
	-D APR_HAS_OTHER_CHILD
	-D AP_HAVE_RELIABLE_PIPED_LOGS
	-D DYNAMIC_MODULE_LIMIT=256
	-D HTTPD_ROOT="/etc/apache2"
	-D SUEXEC_BIN="/usr/lib/apache2/suexec"
	-D DEFAULT_PIDLOG="/var/run/apache2.pid"
	-D DEFAULT_SCOREBOARD="logs/apache_runtime_status"
	-D DEFAULT_ERRORLOG="logs/error_log"
	-D AP_TYPES_CONFIG_FILE="mime.types"
	-D SERVER_CONFIG_FILE="apache2.conf"
```



