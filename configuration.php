<?php
class JConfig {
	public string $offline = '0';
	public string $offline_message = 'This site is down for maintenance.<br />Please check back again soon.';
	public string $display_offline_message = '1';
	public string $offline_image = '';
	public string $sitename = 'Dytech';
	public string $editor = 'tinymce';
	public string $captcha = '0';
	public string $list_limit = '20';
	public string $access = '1';
	public string $debug = '1';
	public string $debug_lang = '0';
	public string $debug_lang_const = '1';
	public string $dbtype = 'mysqli';
	public string $host = '127.0.0.1';
	public string $user = 'homestead';
	public string $password = 'secret';
	public string $db = 'joomla3';
	public string $dbprefix = 'jum_';
	public string $live_site = '';
	public string $secret = '86MgeoAenqe54yPn';
	public string $gzip = '0';
	public string $error_reporting = 'default';
	public string $helpurl = 'https://help.joomla.org/proxy?keyref=Help{major}{minor}:{keyref}&lang={langcode}';
	public string $ftp_host = '';
	public string $ftp_port = '';
	public string $ftp_user = '';
	public string $ftp_pass = '';
	public string $ftp_root = '';
	public string $ftp_enable = '0';
	public string $offset = 'UTC';
	public string $mailonline = '1';
	public string $mailer = 'mail';
	public string $mailfrom = 'mweadennis2@gmail.com';
	public string $fromname = 'Dytech';
	public string $sendmail = '/usr/sbin/sendmail';
	public string $smtpauth = '0';
	public string $smtpuser = '';
	public string $smtppass = '';
	public string $smtphost = 'localhost';
	public string $smtpsecure = 'none';
	public string $smtpport = '25';
	public string $caching = '0';
	public string $cache_handler = 'file';
	public string $cachetime = '15';
	public string $cache_platformprefix = '0';
	public string $MetaDesc = '';
	public string $MetaKeys = '';
	public string $MetaTitle = '1';
	public string $MetaAuthor = '1';
	public string $MetaVersion = '0';
	public string $robots = '';
	public string $sef = '1';
	public string $sef_rewrite = '0';
	public string $sef_suffix = '0';
	public string $unicodeslugs = '0';
	public string $feed_limit = '10';
	public string $feed_email = 'none';
	public string $log_path = '/home/vagrant/code/joomla3/administrator/logs';
	public string $tmp_path = '/home/vagrant/code/joomla3/tmp';
	public string $lifetime = '15';
	public string $session_handler = 'database';
	public string $shared_session = '0';
}