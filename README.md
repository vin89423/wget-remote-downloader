# wget-remote-downloader
Home server is a common device nowadays, some of the NAS also contain manageable Linux system. I have a home server too, I use it as a 24x7 router. I think that to do more things, so i build this remote download. Linux wget is a simple & fast download too. what i need to do is build a web interface for me to access anywhere without ssh.

# Requirement
* Linux apache-server accept wget, ls, head & tail command.
* > Php5

# How to use
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
