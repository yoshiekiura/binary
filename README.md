# How To Install
* install framework properly in /var/www/html/master
* install these files in /var/www/html/binary
* install [gearman](http://gearman.org/getting-started/ "how to install async") and [async](https://github.com/esoftplay/async "esoftplay background process") [optional]
* user crontab:
	* `* * * * * /usr/bin/curl [http://domain.ext/]bin/fix`
	* example: `* * * * * /usr/bin/curl http://localhost/project-path/bin/fix`

# How To Develop
* all functions in `_function.hookable.php` are hookable functions, you can create any module and create function to hook
* functions ending with `_validate` must return boolean (true/false) and its used for validation purpose

# How To Create Modules
* please read `../index.php` for more information

# How To Create Project
* `cd /path/to/new/project`
* `curl -s fisip.net/fw/binary|php|sh`
	* and change the absolute path for binary and master in variable `_MST` inside `config.php`

