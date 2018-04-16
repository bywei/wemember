<?php
/* r_reporting(7);
date_default_timezone_set('UTC');
define("WWW_ROOT", "/home/wwwroot/test/");
define("LOG_FILE", "/www/logs/linroid.com/git-hook.log");
$shell = sprintf("cd %s && /usr/bin/git pull 2>&1", WWW_ROOT);
$output = shell_exec($shell);
$log = sprintf("[%s] %s \n", date('Y-m-d H:i:s', time()), $output);
echo $log;
file_put_contents(LOG_FILE, $log, FILE_APPEND);
*/
//echo shell_exec("id -a");
echo "beging git sync<br/>";
 
// system("/home/wwwroot/default/shell/hirebate.sh");
$output=shell_exec("/data/html/wemember/syncshell/shell.sh");

echo '<pre>'.$output.'</pre><br/>';

echo "end git sync<br/>";

?>
