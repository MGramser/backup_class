```php
php code ->

require 'your_path/backup_autoload.php';

$backupweb = new backupWeb;											// create new instance
$backupweb -> backup_base = '/';									// set the folder to be backup'ed (use '/' to backup your public root folder)
$backupweb -> backup_base_name = 'yourwebsite';						// set the basename of your backup (timestamp wil be added)

$backupweb -> set_db(HOST, USERNAME, PASSWORD, NAME);				// (optional) use this if you want to backup your database
$backupweb -> set_mail(server, username, password, port, to);		// (optional) use this if you want to send your database over mail
$backupweb -> set_ftp(server, username, password, path);			// (optional) use this if you want to send your backup to an external ftp server
$backupweb -> set_local(path, delete_old_days);						// (optional) use this if you want to backup locally (and delete old backups)

$backupweb -> backup();												// execute
//$backupweb -> debug();											// (optional) debug the process
```