<?php
// Functions to be called in install and upgrade scripts

function upgrade_all() {
	upgrade_071();
	upgrade_072();
	upgrade_100();
	upgrade_101();
}

// General
function maybe_create_table($table_name, $create_ddl) {
    global $wpdb;
    foreach ($wpdb->get_col("SHOW TABLES",0) as $table ) {
        if ($table == $table_name) {
            return true;
        }
    }
    //didn't find it try to create it.
    $q = $wpdb->query($create_ddl);
    // we cannot directly tell that whether this succeeded!
    foreach ($wpdb->get_col("SHOW TABLES",0) as $table ) {
        if ($table == $table_name) {
            return true;
        }
    }
    return false;
}

function drop_index($table, $index) {
	global $wpdb;
	$wpdb->hide_errors();
	$wpdb->query("ALTER TABLE `$table` DROP INDEX `$index`");
	// Now we need to take out all the extra ones we may have created
	for ($i = 0; $i < 25; $i++) {
		$wpdb->query("ALTER TABLE `$table` DROP INDEX `{$index}_$i`");
	}
	$wpdb->show_errors();
	return true;
}

function add_clean_index($table, $index) {
	global $wpdb;
	drop_index($table, $index);
	$wpdb->query("ALTER TABLE `$table` ADD INDEX ( `$index` )");
	return true;
}

/**
 ** maybe_add_column()
 ** Add column to db table if it doesn't exist.
 ** Returns:  true if already exists or on successful completion
 **           false on error
 */
function maybe_add_column($table_name, $column_name, $create_ddl) {
    global $wpdb, $debug;
    foreach ($wpdb->get_col("DESC $table_name", 0) as $column ) {
        if ($debug) echo("checking $column == $column_name<br />");
        if ($column == $column_name) {
            return true;
        }
    }
    //didn't find it try to create it.
    $q = $wpdb->query($create_ddl);
    // we cannot directly tell that whether this succeeded!
    foreach ($wpdb->get_col("DESC $table_name", 0) as $column ) {
        if ($column == $column_name) {
            return true;
        }
    }
    return false;
}

// .71 stuff

function upgrade_071() {
	global $tableposts;
	maybe_add_column($tableposts, 'post_status', "ALTER TABLE $tableposts ADD `post_status` ENUM('publish','draft','private') NOT NULL");
	maybe_add_column($tableposts, 'comment_status', "ALTER TABLE $tableposts ADD `comment_status` ENUM('open','closed') NOT NULL");
	maybe_add_column($tableposts, 'ping_status', "ALTER TABLE $tableposts ADD `ping_status` ENUM('open','closed') NOT NULL");
	maybe_add_column($tableposts, 'post_password', "ALTER TABLE $tableposts ADD post_password varchar(20) NOT NULL");
}

function upgrade_072() {
	global $wpdb, $tableposts, $tablelinks, $tablelinkcategories, $tableoptions, $tableoptiontypes, $tableoptiongroups, $tableoptiongroup_options, $tableoptionvalues, $tablecategories;
	maybe_add_column($tablelinks, 'link_notes', "ALTER TABLE $tablelinks ADD COLUMN link_notes MEDIUMTEXT NOT NULL DEFAULT '' ");
	maybe_add_column($tablelinkcategories, 'show_images', "ALTER TABLE $tablelinkcategories ADD COLUMN show_images enum('Y','N') NOT NULL default 'Y'");
	maybe_add_column($tablelinkcategories, 'show_description', "ALTER TABLE $tablelinkcategories ADD COLUMN show_description enum('Y','N') NOT NULL default 'Y'");
	maybe_add_column($tablelinkcategories, 'show_rating', "ALTER TABLE $tablelinkcategories ADD COLUMN show_rating enum('Y','N') NOT NULL default 'Y'");
	maybe_add_column($tablelinkcategories, 'show_updated', "ALTER TABLE $tablelinkcategories ADD COLUMN show_updated enum('Y','N') NOT NULL default 'Y'");
	maybe_add_column($tablelinkcategories, 'sort_order', "ALTER TABLE $tablelinkcategories ADD COLUMN sort_order varchar(64) NOT NULL default 'name'");
	maybe_add_column($tablelinkcategories, 'sort_desc', "ALTER TABLE $tablelinkcategories ADD COLUMN sort_desc enum('Y','N') NOT NULL default 'N'");
	maybe_add_column($tablelinkcategories, 'text_before_link', "ALTER TABLE $tablelinkcategories ADD COLUMN text_before_link varchar(128) not null default '<li>'");
	maybe_add_column($tablelinkcategories, 'text_after_link', "ALTER TABLE $tablelinkcategories ADD COLUMN text_after_link  varchar(128) not null default '<br />'");
	maybe_add_column($tablelinkcategories, 'text_after_all', "ALTER TABLE $tablelinkcategories ADD COLUMN text_after_all  varchar(128) not null default '</li>'");
	maybe_add_column($tablelinkcategories, 'list_limit', "ALTER TABLE $tablelinkcategories ADD COLUMN list_limit int not null default -1");
	maybe_add_column($tableposts, 'post_lon', "ALTER TABLE $tableposts ADD COLUMN post_lon float");
	maybe_add_column($tableposts, 'post_lat', "ALTER TABLE $tableposts ADD COLUMN post_lat float ");
	maybe_create_table($tableoptions, "
	CREATE TABLE $tableoptions (
	  option_id int(11) NOT NULL auto_increment,
	  blog_id int(11) NOT NULL default 0,
	  option_name varchar(64) NOT NULL default '',
	  option_can_override enum('Y','N') NOT NULL default 'Y',
	  option_type int(11) NOT NULL default 1,
	  option_value varchar(255) NOT NULL default '',
	  option_width int NOT NULL default 20,
	  option_height int NOT NULL default 8,
	  option_description tinytext NOT NULL default '',
	  option_admin_level int NOT NULL DEFAULT '1',
	  PRIMARY KEY (option_id, blog_id, option_name)
	)
	");
	maybe_create_table($tableoptiontypes, "
	CREATE TABLE $tableoptiontypes (
	  optiontype_id int(11) NOT NULL auto_increment,
	  optiontype_name varchar(64) NOT NULL,
	  PRIMARY KEY (optiontype_id)
	)
	");
	maybe_create_table($tableoptiongroups, "
	CREATE TABLE $tableoptiongroups (
	  group_id int(11) NOT NULL auto_increment,
	  group_name varchar(64) not null,
	  group_desc varchar(255),
	  group_longdesc tinytext,
	  PRIMARY KEY (group_id)
	)
	");
	maybe_create_table($tableoptiongroup_options, "
	CREATE TABLE $tableoptiongroup_options (
	  group_id int(11) NOT NULL,
	  option_id int(11) NOT NULL,
	  seq int(11) NOT NULL,
	  PRIMARY KEY (group_id, option_id)
	)
	");
	maybe_create_table($tableoptionvalues, "
	CREATE TABLE $tableoptionvalues (
	  option_id int(11) NOT NULL,
	  optionvalue tinytext,
	  optionvalue_desc varchar(255),
	  optionvalue_max int(11),
	  optionvalue_min int(11),
	  optionvalue_seq int(11),
	  UNIQUE (option_id, optionvalue(255)),
	  INDEX (option_id, optionvalue_seq)
	)
	");

	// TODO: REWRITE THIS
	$option_types = array(
		"1" => "INSERT INTO $tableoptiontypes (optiontype_id, optiontype_name) VALUES ('1', 'integer')",
		"2" => "INSERT INTO $tableoptiontypes (optiontype_id, optiontype_name) VALUES ('2', 'boolean')",
		"3" => "INSERT INTO $tableoptiontypes (optiontype_id, optiontype_name) VALUES ('3', 'string')",
		"4" => "INSERT INTO $tableoptiontypes (optiontype_id, optiontype_name) VALUES ('4', 'date')",
		"5" => "INSERT INTO $tableoptiontypes (optiontype_id, optiontype_name) VALUES ('5', 'select')",
		"6" => "INSERT INTO $tableoptiontypes (optiontype_id, optiontype_name) VALUES ('6', 'range')",
		"7" => "INSERT INTO $tableoptiontypes (optiontype_id, optiontype_name) VALUES ('7', 'sqlselect')");

	foreach ($option_types as $option_id => $query) {
		if(!$wpdb->get_var("SELECT * FROM $tableoptiontypes WHERE optiontype_id = '$option_id'")) {
			$wpdb->query($query);
		}
	}

	// Guess a site URI
$guessurl = preg_replace('|/wp-admin/.*|i', '', 'http://' . $HTTP_HOST . $REQUEST_URI);
	$option_data = array(		//base options from b2cofig
		"1" => "INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES (1,'siteurl', 3, '$guessurl', '_LANG_INST_BASE_VALUE1', 8, 30)",
		"2" => "INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES (2,'blogfilename', 3, 'index.php', '_LANG_INST_BASE_VALUE2', 8, 20)",
		"3" => "INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES (3,'blogname', 3, 'My WordPress', '_LANG_INST_BASE_VALUE3', 8, 20)",
		"4" => "INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES (4,'blogdescription', 3, 'babblings!', '_LANG_INST_BASE_VALUE4', 8, 40)",
		//"INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES (6,'search_engine_friendly_urls', 2, '0', 'Querystring Configuration ** (don\'t change if you don\'t know what you\'re doing)', 8, 20)",
		"7" => "INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES (7,'new_users_can_blog', 2, '0', '_LANG_INST_BASE_VALUE7', 8, 20)",
		"8" => "INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES (8,'users_can_register', 2, '1', '_LANG_INST_BASE_VALUE8', 8, 20)",
		"54" => "INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES (54,'admin_email', 3, 'you@example.com', '_LANG_INST_BASE_VALUE54', 8, 20)",
		// general blog setup
		"9" => "INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES (9 ,'start_of_week', 5, '1', '_LANG_INST_BASE_VALUE9', 8, 20)",
		//"INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES (10,'use_preview', 2, '1', 'Do you want to use the \'preview\' function', 8, 20)",
		"11" => "INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES (11,'use_bbcode', 2, '0', '_LANG_INST_BASE_VALUE11', 8, 20)",
		"12" => "INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES (12,'use_gmcode', 2, '0', '_LANG_INST_BASE_VALUE12', 8, 20)",
		"13" => "INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES (13,'use_quicktags', 2, '1', '_LANG_INST_BASE_VALUE13', 8, 20)",
		"14" => "INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES (14,'use_htmltrans', 2, '0', '_LANG_INST_BASE_VALUE14', 8, 20)",
		"15" => "INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES (15,'use_balanceTags', 2, '1', '_LANG_INST_BASE_VALUE15', 8, 20)",
		"16" => "INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES (16,'use_smilies', 2, '1', '_LANG_INST_BASE_VALUE16', 8, 20)",
		"17" => "INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES (17,'smilies_directory', 3, 'http://example.com/wp-images/smilies', '_LANG_INST_BASE_VALUE17', 8, 40)",
		"18" => "INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES (18,'require_name_email', 2, '0', '_LANG_INST_BASE_VALUE18', 8, 20)",
		"20" => "INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES (20,'comments_notify', 2, '1', '_LANG_INST_BASE_VALUE20', 8, 20)",
		//rss/rdf feeds
		"21" => "INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES (21,'posts_per_rss', 1, '10', '_LANG_INST_BASE_VALUE21', 8, 20)",
		"22" => "INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES (22,'rss_language', 3, 'ja', '_LANG_INST_BASE_VALUE22', 8, 20)",
		"23" => "INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES (23,'rss_encoded_html', 2, '0', '_LANG_INST_BASE_VALUE23', 8, 20)",
		"24" => "INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES (24,'rss_excerpt_length', 1, '50', '_LANG_INST_BASE_VALUE24', 8, 20)",
		"25" => "INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES (25,'rss_use_excerpt', 2, '1', '_LANG_INST_BASE_VALUE25', 8, 20)",
		"26" => "INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES (26,'use_weblogsping', 2, '0', '_LANG_INST_BASE_VALUE26', 8, 20)",
		"27" => "INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES (27,'use_blodotgsping', 2, '0', '_LANG_INST_BASE_VALUE27', 8, 20)",
		"29" => "INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES (29,'use_trackback', 2, '1', '_LANG_INST_BASE_VALUE29', 8, 20)",
		"30" => "INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES (30,'use_pingback', 2, '1', '_LANG_INST_BASE_VALUE30', 8, 20)",
		//file upload
		"31" => "INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES (31,'use_fileupload', 2, '1', '_LANG_INST_BASE_VALUE31', 8, 20)",
		"32" => "INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES (32,'fileupload_realpath', 3, '/home/your/site/wordpress/images', '_LANG_INST_BASE_VALUE32', 8, 40)",
		"33" => "INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES (33,'fileupload_url', 3, 'http://example.com/images', '_LANG_INST_BASE_VALUE33', 8, 40)",
		"34" => "INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES (34,'fileupload_allowedtypes', 3, ' jpg gif png ', '_LANG_INST_BASE_VALUE34', 8, 20)",
		"35" => "INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES (35,'fileupload_maxk', 1, '96', '_LANG_INST_BASE_VALUE35', 8, 20)",
		"36" => "INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES (36,'fileupload_minlevel', 1, '1', '_LANG_INST_BASE_VALUE36', 8, 20)",
		"37" => "INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES (37,'fileupload_allowedusers', 3, '', '_LANG_INST_BASE_VALUE37', 8, 30)",
		// email settings
		"38" => "INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES (38,'mailserver_url', 3, 'mail.example.com', '_LANG_INST_BASE_VALUE38', 8, 20)",
		"39" => "INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES (39,'mailserver_login', 3, 'login@example.com', '_LANG_INST_BASE_VALUE39', 8, 20)",
		"40" => "INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES (40,'mailserver_pass', 3, 'password', '_LANG_INST_BASE_VALUE40', 8, 20)",
		"41" => "INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES (41,'mailserver_port', 1, '110', '_LANG_INST_BASE_VALUE41', 8, 20)",
		"42" => "INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES (42,'default_category', 1, '1', '_LANG_INST_BASE_VALUE42', 8, 20)",
		"43" => "INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES (43,'subjectprefix', 3, 'blog:', '_LANG_INST_BASE_VALUE43', 8, 20)",
		"44" => "INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES (44,'bodyterminator', 3, '___', '_LANG_INST_BASE_VALUE44', 8, 20)",
		"46" => "INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES (46,'use_phoneemail', 2, '0', '_LANG_INST_BASE_VALUE46', 8, 20)",
		"47" => "INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES (47,'phoneemail_separator', 3, ':::', '_LANG_INST_BASE_VALUE47', 8, 20)",

		// default post stuff

		"55" => "INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(55,'default_post_status',    5, 'publish', '_LANG_INST_BASE_VALUE55', 8, 20)",
		"56" => "INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(56,'default_comment_status', 5, 'open', '_LANG_INST_BASE_VALUE56', 8, 20)",
		"57" => "INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(57,'default_ping_status',    5, 'open', '_LANG_INST_BASE_VALUE57', 8, 20)",
		"58" => "INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(58,'default_pingback_flag',  5, '1', '_LANG_INST_BASE_VALUE58', 8, 20)",
		"59" => "INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(59,'default_post_category',  7, '1', '_LANG_INST_BASE_VALUE59', 8, 20)",
		"83" => "INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(83,'default_post_edit_rows', 1, '9', '_LANG_INST_BASE_VALUE83', 8, 5)",

		// original options from options page
		"48" => "INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES (48,'posts_per_page', 1, '20','_LANG_INST_BASE_VALUE48', 4, 20)",
		"49" => "INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES (49,'what_to_show', 5, 'posts','_LANG_INST_BASE_VALUE49', 4, 20)",
		"50" => "INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES (50,'archive_mode', 5, 'monthly','_LANG_INST_BASE_VALUE50', 4, 20)",
		"51" => "INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES (51,'time_difference', 6, '0', '_LANG_INST_BASE_VALUE51', 4, 20)",
		"52" => "INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES (52,'date_format', 3, 'n/j/Y', '_LANG_INST_BASE_VALUE52', 4, 20)",
		"53" => "INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES (53,'time_format', 3, 'g:i a', '_LANG_INST_BASE_VALUE53', 4, 20)",
		"84" => "INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES (84,'use_geo_positions', 2, '0', '_LANG_INST_BASE_VALUE84', 8, 20)",
		"85" => "INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES (85,'use_default_geourl', 2, '1','_LANG_INST_BASE_VALUE85', 8, 20)",
		"86" => "INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES (86,'default_geourl_lat ', 8, 0.0, '_LANG_INST_BASE_VALUE86', 8, 20)",
		"87" => "INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES (87,'default_geourl_lon', 8, 0.0, '_LANG_INST_BASE_VALUE87', 8, 20)",
		"60" => "INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES (60,'links_minadminlevel',             1, '5', '_LANG_INST_BASE_VALUE60', 8, 10)",
		"61" => "INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES (61,'links_use_adminlevels',           2, '1', '_LANG_INST_BASE_VALUE61', 8, 20)",
		"62" => "INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES (62,'links_rating_type',               5, 'image', '_LANG_INST_BASE_VALUE62', 8, 10)",
		"63" => "INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES (63,'links_rating_char',               3, '*', '_LANG_INST_BASE_VALUE63', 8, 5)",
		"64" => "INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES (64,'links_rating_ignore_zero',        2, '1', '_LANG_INST_BASE_VALUE64', 8, 20)",
		"65" => "INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES (65,'links_rating_single_image',       2, '1', '_LANG_INST_BASE_VALUE65', 8, 20)",
		"66" => "INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES (66,'links_rating_image0',             3, 'wp-links/links-images/tick.png', '_LANG_INST_BASE_VALUE66', 8, 40)",
		"67" => "INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES (67,'links_rating_image1',             3, 'wp-links/links-images/rating-1.gif', '_LANG_INST_BASE_VALUE67', 8, 40)",
		"68" => "INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES (68,'links_rating_image2',             3, 'wp-links/links-images/rating-2.gif', '_LANG_INST_BASE_VALUE68', 8, 40)",
		"69" => "INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES (69,'links_rating_image3',             3, 'wp-links/links-images/rating-3.gif', '_LANG_INST_BASE_VALUE69', 8, 40)",
		"70" => "INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES (70,'links_rating_image4',             3, 'wp-links/links-images/rating-4.gif', '_LANG_INST_BASE_VALUE70', 8, 40)",
		"71" => "INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES (71,'links_rating_image5',             3, 'wp-links/links-images/rating-5.gif', '_LANG_INST_BASE_VALUE71', 8, 40)",
		"72" => "INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES (72,'links_rating_image6',             3, 'wp-links/links-images/rating-6.gif', '_LANG_INST_BASE_VALUE72', 8, 40)",
		"73" => "INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES (73,'links_rating_image7',             3, 'wp-links/links-images/rating-7.gif', '_LANG_INST_BASE_VALUE73', 8, 40)",
		"74" => "INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES (74,'links_rating_image8',             3, 'wp-links/links-images/rating-8.gif', '_LANG_INST_BASE_VALUE74', 8, 40)",
		"75" => "INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES (75,'links_rating_image9',             3, 'wp-links/links-images/rating-9.gif', '_LANG_INST_BASE_VALUE75', 8, 40)",
		"76" => "INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES (76,'weblogs_cache_file',              3, 'weblogs.com.changes.cache', '_LANG_INST_BASE_VALUE76', 8, 40)",
		"77" => "INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES (77,'weblogs_xml_url',                 3, 'http://www.weblogs.com/changes.xml', '_LANG_INST_BASE_VALUE77', 8, 40)",
		"78" => "INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES (78,'weblogs_cacheminutes',            1, '60', '_LANG_INST_BASE_VALUE78', 8, 10)",
		"79" => "INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES (79,'links_updated_date_format',       3, 'd/m/Y h:i', '_LANG_INST_BASE_VALUE79', 8, 25)",
		"80" => "INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES (80,'links_recently_updated_prepend',  3, '&gt;&gt;', '_LANG_INST_BASE_VALUE80', 8, 10)",
		"81" => "INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES (81,'links_recently_updated_append',   3, '&lt;&lt;', '_LANG_INST_BASE_VALUE81', 8, 20)",
		"82" => "INSERT INTO $tableoptions (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES (82,'links_recently_updated_time',     1, '120', '_LANG_INST_BASE_VALUE82', 8, 20)"
		);

	foreach ($option_data as $option_id => $query) {
		if(!$wpdb->get_var("SELECT * FROM $tableoptions WHERE option_id = '$option_id'")) {
			$wpdb->query($query);
		}
	}

	$option_groups = array(
	"INSERT INTO $tableoptiongroups (group_id,  group_name, group_desc) VALUES (1, 'Other Options', '_LANG_INST_BASE_HELP1')",
	"INSERT INTO $tableoptiongroups (group_id,  group_name, group_desc) VALUES (2, 'General blog settings', '_LANG_INST_BASE_HELP2')",
	"INSERT INTO $tableoptiongroups (group_id,  group_name, group_desc) VALUES (3, 'RSS/RDF Feeds, Track/Ping-backs', '_LANG_INST_BASE_HELP3')",
	"INSERT INTO $tableoptiongroups (group_id,  group_name, group_desc) VALUES (4, 'File uploads', '_LANG_INST_BASE_HELP4')",
	"INSERT INTO $tableoptiongroups (group_id,  group_name, group_desc) VALUES (5, 'Blog-by-Email settings', '_LANG_INST_BASE_HELP5')",
	"INSERT INTO $tableoptiongroups (group_id,  group_name, group_desc) VALUES (6, 'Base settings', '_LANG_INST_BASE_HELP6')",
	"INSERT INTO $tableoptiongroups (group_id,  group_name, group_desc) VALUES (7, 'Default post options', '_LANG_INST_BASE_HELP7')",
	"INSERT INTO $tableoptiongroups (group_id,  group_name, group_desc) VALUES (8, 'Link Manager Settings', '_LANG_INST_BASE_HELP8')",
	"INSERT INTO $tableoptiongroups (group_id, group_name, group_desc) VALUES (9, 'Geo Options', '_LANG_INST_BASE_HELP9')");

	foreach ($option_groups as $query) {
		$option_id = preg_match('|VALUES \(([0-9]+)|', $query, $matches);
		$option_id = $matches[1];
		if(!$wpdb->get_var("SELECT * FROM $tableoptiongroups WHERE group_id = '$option_id'")) {
			$wpdb->query($query);
			}
	}

	$optiongroup_options = array (		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (1,48,1 )",
		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (1,49,2 )",
		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (1,50,3 )",
		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (1,51,4 )",
		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (1,52,5 )",
		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (1,53,6 )",


		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (2,9 ,1 )",
		//"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (2,10,2 )",
		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (2,11,3 )",
		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (2,12,4 )",
		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (2,13,5 )",
		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (2,14,6 )",
		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (2,15,7 )",
		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (2,16,8 )",
		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (2,17,9 )",
		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (2,18,10)",
		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (2,19,11)",
		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (2,20,12)",


		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (3,21,1 )",
		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (3,22,2 )",
		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (3,23,3 )",
		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (3,24,4 )",
		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (3,25,5 )",
		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (3,26,6 )",
		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (3,27,7 )",
		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (3,28,8 )",
		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (3,29,9 )",
		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (3,30,10)",


		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (4,31,1 )",
		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (4,32,2 )",
		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (4,33,3 )",
		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (4,34,4 )",
		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (4,35,5 )",
		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (4,36,6 )",
		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (4,37,7 )",


		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (5,38,1 )",
		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (5,39,2 )",
		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (5,40,3 )",
		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (5,41,4 )",
		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (5,42,5 )",
		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (5,43,6 )",
		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (5,44,7 )",
		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (5,45,8 )",
		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (5,46,9 )",
		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (5,47,10)",


		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (6,1,1)",
		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (6,2,2)",
		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (6,3,3)",
		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (6,4,4)",
		//"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (6,6,5)",
		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (6,7,6)",
		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (6,8,7)",
		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (6,54,8)",
		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (7,55,1 )",
		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (7,56,2 )",
		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (7,57,3 )",
		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (7,58,4 )",
		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (7,59,5 )",
		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (7,83,5 )",
		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (8,60,1 )",
		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (8,61,2 )",
		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (8,62,3 )",
		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (8,63,4 )",
		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (8,64,5 )",
		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (8,65,6 )",
		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (8,66,7 )",
		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (8,67,8 )",
		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (8,68,9 )",
		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (8,69,10)",
		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (8,70,11)",
		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (8,71,12)",
		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (8,72,13)",
		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (8,73,14)",
		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (8,74,15)",
		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (8,75,16)",
		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (8,76,17)",
		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (8,77,18)",
		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (8,78,19)",
		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (8,79,20)",
		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (8,80,21)",
		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (8,81,22)",
		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (8,82,23)",
			"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (9,84,1)",
		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (9,85,1)",
		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (9,86,1)",
		"INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) VALUES (9,87,1)",
	);

	foreach ($optiongroup_options as $query) {
		preg_match('|VALUES \([0-9]+,([0-9]+)|', $query, $matches);
		$option_id = $matches[1];
		if(!$wpdb->get_var("SELECT * FROM $tableoptiongroup_options WHERE option_id = '$option_id'")) {
			$wpdb->query($query);
			}
	}

	$option_values = array(
		// select data for what to show
		"INSERT INTO $tableoptionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (49, 'days',  'days',        null,null,1)",
		"INSERT INTO $tableoptionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (49, 'posts', 'posts',       null,null,2)",
		"INSERT INTO $tableoptionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (49, 'paged', 'posts paged', null,null,3)",
		// select data for archive mode
		"INSERT INTO $tableoptionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (50, 'daily',     'daily',       null,null,1)",
		"INSERT INTO $tableoptionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (50, 'weekly',    'weekly',      null,null,2)",
		"INSERT INTO $tableoptionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (50, 'monthly',   'monthly',     null,null,3)",
		"INSERT INTO $tableoptionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (50, 'postbypost','post by post',null,null,4)",
		// select data for time diff
		"INSERT INTO $tableoptionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (51, 'hours', 'hours', 23, -23, null)",
		// select data for start of week
		"INSERT INTO $tableoptionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (9, '0', 'Sunday',   null,null,1)",
		"INSERT INTO $tableoptionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (9, '1', 'Monday',   null,null,2)",
		"INSERT INTO $tableoptionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (9, '6', 'Saturday', null,null,3)",


		// Add in a new page for POST DEFAULTS

		// default_post_status  select one of publish draft private
		// default_comment_status select one of open closed
		// default_ping_status select one of open closed
		// default_pingback_flag select one of checked unchecked
		// default_post_category sql_select "SELECT cat_id AS value, cat_name AS label FROM $tablecategories order by cat_name"




		// select data for post_status
		"INSERT INTO $tableoptionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (55, 'publish', 'Publish', null,null,1)",
		"INSERT INTO $tableoptionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (55, 'draft',   'Draft',   null,null,2)",
		"INSERT INTO $tableoptionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (55, 'private', 'Private', null,null,3)",

		// select data for comment_status
		"INSERT INTO $tableoptionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (56, 'open', 'Open',   null,null,1)",
		"INSERT INTO $tableoptionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (56, 'closed', 'Closed', null,null,2)",

		// select data for ping_status (aargh duplication!)
		"INSERT INTO $tableoptionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (57, 'open', 'Open',   null,null,1)",
		"INSERT INTO $tableoptionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (57, 'closed', 'Closed', null,null,2)",

		// select data for pingback flag
		"INSERT INTO $tableoptionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (58, '1', 'Checked',   null,null,1)",
		"INSERT INTO $tableoptionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (58, '0', 'Unchecked', null,null,2)",

		// sql select data for default
		"INSERT INTO $tableoptionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (59, 'SELECT cat_id AS value, cat_name AS label FROM $tablecategories order by cat_name', '', null,null,1)",
		"INSERT INTO $tableoptionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (62, 'number', 'Number',    null,null,1)",
		"INSERT INTO $tableoptionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (62, 'char',   'Character', null,null,2)",
		"INSERT INTO $tableoptionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (62, 'image',  'Image',     null,null,3)"
		);

	foreach ($option_values as $query) {
		preg_match("|VALUES \(([0-9]+), '([^']+)'|", $query, $matches);
		$option_id = $matches[1];
		$value = $matches[2];
		if(!$wpdb->get_var("SELECT * FROM $tableoptionvalues WHERE option_id = '$option_id' AND optionvalue = '$value'")) {
			$wpdb->query($query);
			}
	}


	    if (file_exists('../wp-links/links.config.php')) {
        include('../wp-links/links.config.php');

        // now update the database with those settings
        $query = "UPDATE $tableoptions SET option_value='".addslashes($links_minadminlevel           )."' WHERE option_id=60"; $q = $wpdb->query($query);
        $query = "UPDATE $tableoptions SET option_value='".addslashes($links_use_adminlevels         )."' WHERE option_id=61"; $q = $wpdb->query($query);
        $query = "UPDATE $tableoptions SET option_value='".addslashes($links_rating_type             )."' WHERE option_id=62"; $q = $wpdb->query($query);
        $query = "UPDATE $tableoptions SET option_value='".addslashes($links_rating_char             )."' WHERE option_id=63"; $q = $wpdb->query($query);
        $query = "UPDATE $tableoptions SET option_value='".addslashes($links_rating_ignore_zero      )."' WHERE option_id=64"; $q = $wpdb->query($query);
        $query = "UPDATE $tableoptions SET option_value='".addslashes($links_rating_single_image     )."' WHERE option_id=65"; $q = $wpdb->query($query);
        $query = "UPDATE $tableoptions SET option_value='".addslashes($links_rating_image0           )."' WHERE option_id=66"; $q = $wpdb->query($query);
        $query = "UPDATE $tableoptions SET option_value='".addslashes($links_rating_image1           )."' WHERE option_id=67"; $q = $wpdb->query($query);
        $query = "UPDATE $tableoptions SET option_value='".addslashes($links_rating_image2           )."' WHERE option_id=68"; $q = $wpdb->query($query);
        $query = "UPDATE $tableoptions SET option_value='".addslashes($links_rating_image3           )."' WHERE option_id=69"; $q = $wpdb->query($query);
        $query = "UPDATE $tableoptions SET option_value='".addslashes($links_rating_image4           )."' WHERE option_id=70"; $q = $wpdb->query($query);
        $query = "UPDATE $tableoptions SET option_value='".addslashes($links_rating_image5           )."' WHERE option_id=71"; $q = $wpdb->query($query);
        $query = "UPDATE $tableoptions SET option_value='".addslashes($links_rating_image6           )."' WHERE option_id=72"; $q = $wpdb->query($query);
        $query = "UPDATE $tableoptions SET option_value='".addslashes($links_rating_image7           )."' WHERE option_id=73"; $q = $wpdb->query($query);
        $query = "UPDATE $tableoptions SET option_value='".addslashes($links_rating_image8           )."' WHERE option_id=74"; $q = $wpdb->query($query);
        $query = "UPDATE $tableoptions SET option_value='".addslashes($links_rating_image9           )."' WHERE option_id=75"; $q = $wpdb->query($query);
        $query = "UPDATE $tableoptions SET option_value='".addslashes($weblogs_cache_file            )."' WHERE option_id=76"; $q = $wpdb->query($query);
        $query = "UPDATE $tableoptions SET option_value='".addslashes($weblogs_xml_url               )."' WHERE option_id=77"; $q = $wpdb->query($query);
        $query = "UPDATE $tableoptions SET option_value='".addslashes($weblogs_cacheminutes          )."' WHERE option_id=78"; $q = $wpdb->query($query);
        $query = "UPDATE $tableoptions SET option_value='".addslashes($links_updated_date_format     )."' WHERE option_id=79"; $q = $wpdb->query($query);
        $query = "UPDATE $tableoptions SET option_value='".addslashes($links_recently_updated_prepend)."' WHERE option_id=80"; $q = $wpdb->query($query);
        $query = "UPDATE $tableoptions SET option_value='".addslashes($links_recently_updated_append )."' WHERE option_id=81"; $q = $wpdb->query($query);
        $query = "UPDATE $tableoptions SET option_value='".addslashes($links_recently_updated_time   )."' WHERE option_id=82"; $q = $wpdb->query($query);
    // end if links.config.php exists
    }

}

function upgrade_100() {
	global $wpdb, $tableposts, $tablecomments, $tablecategories, $tableoptionvalues, $tableoptiongroups, $tableoptiongroup_options, $tableoptions, $tablepost2cat, $tablelinks, $tableusers;
	maybe_add_column($tableposts, 'post_name', "ALTER TABLE `$tableposts` ADD `post_name` VARCHAR(200) NOT NULL");
	maybe_add_column($tableposts, 'to_ping', "ALTER TABLE $tableposts ADD `to_ping` TEXT NOT NULL");
	maybe_add_column($tableposts, 'pinged', "ALTER TABLE $tableposts ADD `pinged` TEXT NOT NULL");
	maybe_add_column($tableposts, 'post_modified', "ALTER TABLE $tableposts ADD `post_modified` DATETIME NOT NULL");
	maybe_add_column($tableposts, 'post_content_filtered', "ALTER TABLE $tableposts ADD `post_content_filtered` TEXT NOT NULL");
	maybe_add_column($tablecategories, 'category_nicename', "ALTER TABLE `$tablecategories` ADD `category_nicename` VARCHAR(200) NOT NULL");
	maybe_add_column($tablecategories, 'category_description', "ALTER TABLE `$tablecategories` ADD `category_description` TEXT NOT NULL");
	maybe_add_column($tablecategories, 'category_parent', "ALTER TABLE `$tablecategories` ADD `category_parent` INT(4) NOT NULL");
	maybe_add_column($tablelinks, 'link_rss', "ALTER TABLE `$tablelinks` ADD `link_rss` VARCHAR( 255 ) NOT NULL;");
	maybe_add_column($tableusers, 'user_description', "ALTER TABLE `$tableusers` ADD `user_description` TEXT NOT NULL");
	maybe_add_column($tablecomments, 'comment_approved', "ALTER TABLE $tablecomments ADD COLUMN comment_approved ENUM('0', '1') DEFAULT '1' NOT NULL");

	// Create indicies
	add_clean_index($tableposts, 'post_name');
	add_clean_index($tablecategories, 'category_nicename');
	add_clean_index($tablecomments, 'comment_approved');


	// Options stuff
	if (!$wpdb->get_var("SELECT option_id FROM $tableoptions WHERE option_name = 'comment_moderation'")) {
		$wpdb->query("INSERT INTO $tableoptions
			(option_id, blog_id, option_name, option_can_override, option_type, option_value, option_width, option_height, option_description, option_admin_level)
			VALUES
			('0', '0', 'comment_moderation', 'Y', '5',' none', 20, 8, '_LANG_INST_BASE_VALUE88', 8)");
	}

	$oid = $wpdb->get_var("SELECT option_id FROM $tableoptions WHERE option_name = 'comment_moderation'");
	$gid = $wpdb->get_var("SELECT group_id FROM $tableoptiongroups WHERE group_name = 'General blog settings'");
	$seq = $wpdb->get_var("SELECT MAX(seq) FROM $tableoptiongroup_options WHERE group_id = '$gid'");
	++$seq;
	if (!$wpdb->get_row("SELECT * FROM $tableoptiongroup_options WHERE group_id = '$gid' AND option_id = '$oid'")) {
		$wpdb->query("INSERT INTO $tableoptiongroup_options
		(group_id, option_id, seq)
		VALUES
		('$gid', '$oid', '$seq')");
	}

	if (!$wpdb->get_row("SELECT * FROM $tableoptionvalues WHERE option_id = $oid AND optionvalue = 'auto'")) {
		$wpdb->query("INSERT INTO $tableoptionvalues
		(option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq)
		VALUES
		('$oid','auto', 'Automatic', NULL, NULL, 3)");
	}
	if (!$wpdb->get_row("SELECT * FROM $tableoptionvalues WHERE option_id = $oid AND optionvalue = 'none'")) {
		$wpdb->query("INSERT INTO $tableoptionvalues
		(option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq)
		VALUES
		('$oid', 'none', 'None', NULL, NULL, 1)");
	}
	if (!$wpdb->get_row("SELECT * FROM $tableoptionvalues WHERE option_id = $oid AND optionvalue = 'manual'")) {
		$wpdb->query("INSERT INTO $tableoptionvalues
		(option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq)
		VALUES
		('$oid', 'manual', 'Manual', NULL, NULL, 2)");
	}

	if (!$wpdb->get_var("SELECT option_id FROM $tableoptions WHERE option_name = 'moderation_notify'")) {
		$wpdb->query("INSERT INTO $tableoptions
			(option_id, blog_id, option_name, option_can_override, option_type, option_value, option_width, option_height, option_description, option_admin_level)
			VALUES
			('0', '0', 'moderation_notify' , 'Y', '2', '1', 20, 8, '_LANG_INST_BASE_VALUE89', 8)");
	}

	$oid = $wpdb->get_var("SELECT option_id FROM $tableoptions WHERE option_name = 'moderation_notify'");
	$seq = $wpdb->get_var("SELECT MAX(seq) FROM $tableoptiongroup_options WHERE group_id = '$gid'");
	++$seq;
	if (!$wpdb->get_row("SELECT * FROM $tableoptiongroup_options WHERE group_id = '$gid' AND option_id = '$oid'")) {
		$wpdb->query("INSERT INTO $tableoptiongroup_options
			(group_id, option_id, seq)
			VALUES
			('$gid', '$oid', '$seq')");
	}
	// Get the title and ID of every post, post_name to check if it already has a value
	$posts = $wpdb->get_results("SELECT ID, post_title, post_name FROM $tableposts");
	foreach($posts as $post) {
		if ('' == $post->post_name) {
			$newtitle = sanitize_title($post->post_title);
			$wpdb->query("UPDATE $tableposts SET post_name = '$newtitle' WHERE ID = '$post->ID'");
		}
	}

	$categories = $wpdb->get_results("SELECT cat_ID, cat_name, category_nicename FROM $tablecategories");
	foreach ($categories as $category) {
		if ('' == $category->category_nicename) {
			$newtitle = sanitize_title($category->cat_name);
			$wpdb->query("UPDATE $tablecategories SET category_nicename = '$newtitle' WHERE cat_ID = '$category->cat_ID'");
		}
	}

	if (!$wpdb->get_var("SELECT option_name FROM $tableoptions WHERE option_name = 'permalink_structure'")) { // If it's not already there
		$wpdb->query("INSERT INTO `$tableoptions`
			(`option_id`, `blog_id`, `option_name`, `option_can_override`, `option_type`, `option_value`, `option_width`, `option_height`, `option_description`, `option_admin_level`)
			VALUES
			('', '0', 'permalink_structure', 'Y', '3', '', '20', '8', '_LANG_INST_BASE_VALUE90', '8');");
		}

	if (!$wpdb->get_var("SELECT option_name FROM $tableoptions WHERE option_name = 'gzipcompression'")) { // If it's not already there
		$wpdb->query("INSERT INTO `$tableoptions`
			(`option_id`, `blog_id`, `option_name`, `option_can_override`, `option_type`, `option_value`, `option_width`, `option_height`, `option_description`, `option_admin_level`)
			VALUES
			('', '0', 'gzipcompression', 'Y', '2', '0', '20', '8', '_LANG_INST_BASE_VALUE91', '8');");
		$optionid = $wpdb->get_var("SELECT option_id FROM $tableoptions WHERE option_name = 'gzipcompression'");
		$wpdb->query("INSERT INTO $tableoptiongroup_options
			(group_id, option_id, seq)
			VALUES
			(2, $optionid, 5)");
		}
	if (!$wpdb->get_var("SELECT option_id FROM $tableoptions WHERE option_name = 'hack_file'")) {
		$wpdb->query("INSERT INTO `$tableoptions`
			( `option_id` , `blog_id` , `option_name` , `option_can_override` , `option_type` , `option_value` , `option_width` , `option_height` , `option_description` , `option_admin_level` )
			VALUES
			('', '0', 'hack_file', 'Y', '2', '0', '20', '8', '_LANG_INST_BASE_VALUE92', '8')");
		$optionid = $wpdb->get_var("SELECT option_id FROM $tableoptions WHERE option_name = 'hack_file'");
		$wpdb->query("INSERT INTO $tableoptiongroup_options
			(group_id, option_id, seq)
			VALUES
			(2, $optionid, 5)");
	}

	$wpdb->query("UPDATE $tableoptionvalues SET optionvalue_max = 23 , optionvalue_min = -23 WHERE option_id = 51");
	// fix upload users description
	$wpdb->query("UPDATE $tableoptions SET option_description = '_LANG_INST_BASE_VALUE37' WHERE option_id = 37");
	// and file types
	$wpdb->query("UPDATE $tableoptions SET option_description = '_LANG_INST_BASE_VALUE34' WHERE option_id = 34");
	// add link to php date format. this could be to a wordpress.org page in the future
	$wpdb->query("UPDATE $tableoptions SET option_description = '_LANG_INST_BASE_VALUE52' WHERE option_id = 52");
	$wpdb->query("UPDATE $tableoptions SET option_description = '_LANG_INST_BASE_VALUE53' WHERE option_id = 53");
	$wpdb->query("UPDATE $tableoptions SET option_value = REPLACE(option_value, 'wp-links/links-images/', 'wp-images/links/')
                                                      WHERE option_name LIKE 'links_rating_image%'
                                                      AND option_value LIKE 'wp-links/links-images/%'");
	$wpdb->query("DELETE FROM $tableoptions WHERE option_name = 'comment_allowed_tags'");
	$wpdb->query("DELETE FROM $tableoptions WHERE option_name = 'use_preview'");
	$wpdb->query("DELETE FROM $tableoptions WHERE option_name = 'search_engine_friendly_urls'");
	// Multiple categories
	maybe_create_table($tablepost2cat, "
		CREATE TABLE `$tablepost2cat` (
		`rel_id` INT NOT NULL AUTO_INCREMENT ,
		`post_id` INT NOT NULL ,
		`category_id` INT NOT NULL ,
		PRIMARY KEY ( `rel_id` ) ,
		INDEX ( `post_id` , `category_id` )
		)
		");
	$allposts = $wpdb->get_results("SELECT ID, post_category FROM $tableposts");
	foreach ($allposts as $post) {
		// Check to see if it's already been imported
		$cat = $wpdb->get_row("SELECT * FROM $tablepost2cat WHERE post_id = $post->ID AND category_id = $post->post_category");
		if (!$cat && 0 != $post->post_category) { // If there's no result
			$wpdb->query("
				INSERT INTO $tablepost2cat
				(post_id, category_id)
				VALUES
				('$post->ID', '$post->post_category')
				");
		}
	}
}

function upgrade_101() {
	global $wpdb, $tableoptionvalues, $tablelinkcategories, $tableposts, $tablecategories, $tablecomments, $tablelinks;
	// Fix possible duplicate problem from CVS
	$option59 = $wpdb->get_results("SELECT * FROM $tableoptionvalues WHERE option_id  = '59'");
	if (1 < count($option59)) {
		$wpdb->query("DELETE FROM $tableoptionvalues WHERE option_id = '59' AND optionvalue LIKE('%FROM  order%')");
	}

	// Remove 'automatic' option for comment moderation until it actually does something
	$wpdb->query("DELETE FROM $tableoptionvalues WHERE optionvalue = 'auto'");
	// Less intrusive default
	$wpdb->query("ALTER TABLE `$tablelinkcategories` CHANGE `show_description` `show_description` ENUM( 'Y', 'N' ) DEFAULT 'N' NOT NULL");

	// Clean up indices, add a few
	add_clean_index($tableposts, 'post_name');
	add_clean_index($tableposts, 'post_status');
	add_clean_index($tablecategories, 'category_nicename');
	add_clean_index($tablecomments, 'comment_approved');
	add_clean_index($tablecomments, 'comment_post_ID');
	add_clean_index($tablelinks , 'link_category');
	add_clean_index($tablelinks , 'link_visible');
}

?>