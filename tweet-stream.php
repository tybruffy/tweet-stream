<?php

require_once(__DIR__.'/twitteroauth/twitteroauth.php');

Class Tweet_Display {
	public $errors;
	private $username;

	function __construct($username, $api_key, $api_secret, $oath_token, $oauth_secret) {
		$this->username   = $username;
		$this->connection = new TwitterOAuth($api_key, $api_secret, $oath_token, $oauth_secret);
	}

	function get_tweets( $count ) {
		$tweets = $this->connection->get("statuses/user_timeline", array(
			'screen_name' => $this->username,
			'count'       => $count,
		));

		if ( !empty($this->tweets->errors) ) {
			$this->errors = $this->tweets->errors;
			return false;
		}

		return $tweets;
	}

	function display_tweets( $format, $count = 1 ) {
		$tweets = $this->get_tweets( $count );
		
		if ( $tweets ) {
			foreach($tweets as $tweet) {
				echo $this->get_tweet_HTML($tweet, $format);
			}
		}
	}

	private function get_tweet_HTML( $tweet, $format ) {
		$text = $this->convert_entities($tweet);
		return sprintf($format,
			$text,
			'https://twitter.com/'.$this->username."/status/".$tweet->id_str,
			$this->get_relative_time($tweet->created_at),
			'https://twitter.com/intent/tweet?in_reply_to='.$tweet->id_str,
			'https://twitter.com/intent/retweet?tweet_id='.$tweet->id_str,
			'https://twitter.com/intent/favorite?tweet_id='.$tweet->id_str
		);
	}


	private function convert_entities($tweet) {
		// create xhtml safe text (mostly to be safe of ampersands)
		$output = htmlentities(html_entity_decode($tweet->text, ENT_NOQUOTES), ENT_NOQUOTES);

		// URLs
		foreach ($tweet->entities->urls as $url) {
			$old_url        = $url->url;
			$expanded_url   = (empty($url->expanded_url))   ? $url->url : $url->expanded_url;
			$display_url    = (empty($url->display_url))    ? $url->url : $url->display_url;
			$replacement    = '<a href="'.$expanded_url.'" rel="external" target="_blank">'.$display_url.'</a>';
			$output         = str_replace($old_url, $replacement, $output);
		}

		// Hashtags
		foreach ($tweet->entities->hashtags as $hashtags) {
			$hashtag        = '#'.$hashtags->text;
			$replacement    = '<a href="http://twitter.com/search?q=%23'.$hashtags->text.'" rel="external" target="_blank">'.$hashtag.'</a>';
			$output         = str_ireplace($hashtag, $replacement, $output);
		}

		// Usernames
		foreach ($tweet->entities->user_mentions as $user_mentions) {
			$username       = '@'.$user_mentions->screen_name;
			$replacement    = '<a href="http://twitter.com/'.$user_mentions->screen_name.'" rel="external" target="_blank" title="'.$user_mentions->name.' on Twitter">'.$username.'</a>';
			$output         = str_ireplace($username, $replacement, $output);
		}

		return $output;
	}


	private function get_relative_time($date_time) {
		$tprefix = '';
		$tsecs   = 'seconds';
		$tmin    = 'minutes';
		$tmins   = 'minutes';
		$thour   = 'hour';
		$thours  = 'hours';
		$tday    = 'day';
		$tdays   = 'days';
		$tsuffix = 'ago';
		$now     = time();
		
		$when = ($now - strtotime($date_time));
		$posted = "";
		
		if ($when < 60) {
			$posted = $tprefix." ".$when." ".$tsecs." ".$tsuffix;
		}
		if (($posted == "") & ($when < 3600)) {
			$posted = $tprefix." ".(floor($when / 60))." ".$tmins." ".$tsuffix;
		}
		if (($posted == "") & ($when < 7200)) {
			$posted = $tprefix." 1 ".$thour." ".$tsuffix;
		}
		if (($posted == "") & ($when < 86400)) {
			$posted = $tprefix." ".(floor($when / 3600))." ".$thours." ".$tsuffix;
		}
		if (($posted == "") & ($when < 172800)) {
			$posted = $tprefix." 1 ".$tday." ".$tsuffix;
		}
		if ($posted == "") {
			$posted = $tprefix." ".(floor($when / 86400))." ".$tdays." ".$tsuffix;
		}
		
		return $posted;
	}

}