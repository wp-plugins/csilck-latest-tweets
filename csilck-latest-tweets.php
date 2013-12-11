<?php
/**
 * Plugin Name: Latest Tweets
 * Plugin URI:
 * Description: Access the latest tweets from anywhere (user timeline, list, followers, etc).
 * Version: 0.1
 * Author: Colin Burn-Murdoch, IfLooksCouldKill
 * Author URI: http://www.iflookscouldkill.co.uk
 * License: GPLv2 or later
 */

add_action('admin_menu', 'latest_tweets_menu');

function latest_tweets_menu() {
	add_menu_page('Latest Tweets', 'Latest Tweets', 'administrator', __FILE__, 'latest_tweets_html');
	add_action('admin_init', 'register_latesttweetssettings');
}

function register_latesttweetssettings() {
	register_setting('latest_tweets_group', 'consumer_key');
	register_setting('latest_tweets_group', 'consumer_secret');
	register_setting('latest_tweets_group', 'token_key');
	register_setting('latest_tweets_group', 'token_secret');
	register_setting('latest_tweets_group', 'cache_time');
	register_setting('latest_tweets_group', 'method');
	register_setting('latest_tweets_group', 'parameters');
}

function latest_tweets_html() { ?>
<div class="wrap">
	<h2>Latest Tweets Settings</h2>

	<form method="post" action="options.php">
		<?php settings_fields('latest_tweets_group'); ?>
		<table class="form-table">
			<tr valign="top">
				<th>Consumer Key</th>
				<td><input type="text" name="consumer_key" value="<?php echo get_option('consumer_key'); ?>" /></td>
			</tr>

			<tr valign="top">
				<th>Consumer Secret</th>
				<td><input type="text" name="consumer_secret" value="<?php echo get_option('consumer_secret'); ?>" /></td>
			</tr>

			<tr valign="top">
				<th>Token Key</th>
				<td><input type="text" name="token_key" value="<?php echo get_option('token_key'); ?>" /></td>
			</tr>

			<tr valign="top">
				<th>Token Secret</th>
				<td><input type="text" name="token_secret" value="<?php echo get_option('token_secret'); ?>" /></td>
			</tr>

			<tr valign="top">
				<th>Cache Time</th>
				<td><input type="text" name="cache_time" value="<?php echo get_option('cache_time'); ?>" /></td>
			</tr>

			<tr valign="top">
				<th>Method</th>
				<td><input type="text" name="method" value="<?php echo get_option('method'); ?>" /></td>
			</tr>

			<tr valign="top">
				<th>Parameters</th>
				<td><input type="text" name="parameters" value="<?php echo get_option('parameters'); ?>" /></td>
			</tr>
		</table>

		<p class="submit">
			<input type="submit" class="button-primary" value="Save Changes" />
		</p>
	</form>
</div>
<?php }

define('CONSUMER_KEY', get_option('consumer_key'));
define('CONSUMER_SECRET', get_option('consumer_secret'));
define('TOKEN_KEY', get_option('token_key'));
define('TOKEN_SECRET', get_option('token_secret'));
define('CACHE_TIME', 300);

define('METHOD', get_option('method') ? get_option('method') : 'statuses_userTimeline');
define('PARAMETERS', get_option('parameters'));


require_once dirname( __FILE__ ).'/vendor/autoload.php';

class Latest_Tweets {
	private static $cache;
	private static $timeline = array();

	public function __construct() {
		add_option('latest_tweets', array(
			'consumer_key' => '',
			'consumer_secret' => '',
			'token_key' => '',
			'token_secret' => '',
			'cache_time' => '300'
			)
		);
	}

	public static function one() {
		self::load();
		return self::$timeline[0];
	}

	public static function range($limit = null, $offset = 0) {
		self::load();
		return array_slice(self::$timeline,$offset,$limit);
	}

	public static function load() {
		$cache = dirname(__FILE__).'/cache.txt';

		if(time() - filemtime($cache) < CACHE_TIME) {
			self::$timeline = unserialize(file_get_contents($cache));
		} else {
			\Codebird\Codebird::setConsumerKey(CONSUMER_KEY,CONSUMER_SECRET);
			$cb = \Codebird\Codebird::getInstance();
			$cb->setToken(TOKEN_KEY,TOKEN_SECRET);
			$timeline = array();

			$result = $cb->{METHOD}(PARAMETERS);

			if(isset($result->errors)) {
				throw new Exception($result->errors[0]->message, $result->errors[0]->code);
			}

			foreach($result as $tweet) {
				if(isset($tweet->user)) {
					self::$timeline[] = $tweet;
				}
			}
			file_put_contents($cache,serialize(self::$timeline));
		}
	}

	public static function highlight($str) {
		$str = preg_replace('/(https?:\/\/[^ ]*)/', '<a target="_blank" href="$1">$1</a>', $str);
		$str = preg_replace('/@([a-zA-Z0-9_]+)([^a-zA-Z0-9_]|$)/', '<a target="_blank" href="http://twitter.com/$1">@$1</a>$2', $str);
		return $str;
	}
}

// Initialize everything
$latest_tweets = new Latest_Tweets();
