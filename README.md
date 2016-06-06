# Wget Remote Downloader
Home server is a common device nowadays, some of the NAS also contain manageable Linux system. I have a home server too, I use it as a 24x7 router. I think that to do more things, so i build this remote download. Linux wget is a simple & fast download too. what i need to do is build a web interface for me to access anywhere without ssh.

* The downloaded is file based, no database required.

## Screenshot
1. This is how the application look like.
![Alt](https://raw.githubusercontent.com/vin89423/wget-remote-downloader/master/sample-img/download_list.png)

2. Click action button to add download request.
![Alt](https://raw.githubusercontent.com/vin89423/wget-remote-downloader/master/sample-img/download-dialog.png)

3. While file is downloading. The progress will show how much it downloaded.
![Alt](https://raw.githubusercontent.com/vin89423/wget-remote-downloader/master/sample-img/downloading.png)

4. File download completed. When you want this file, you can download it from your own server.
![Alt](https://raw.githubusercontent.com/vin89423/wget-remote-downloader/master/sample-img/downloaded.png)


## Requirement
* A Linux Server that accept wget, ls, head & tail command.
* Apache
* > Php5

## How to use
#### 1. Modify manifest.json
| Param | Description |
| --- | --- |
| url.domain | Hosting domain name. |
| url.root | Url between domain and index.php, if it is in root of web server, put / in this field. |
| session.token | Login cookie name. |
| session.encrypt | Login cookie encryption key. |

#### 2. Modify main/settings/setting.php
| Param | Description |
| --- | --- |
| $SETTING['account'] | Download user and password. |
| $SETTING['file_storage'] | It is where the file will store. |

## TODO
* Add retry mechanism.
* Add download speed next to progress bar.
