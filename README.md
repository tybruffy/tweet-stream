#PHP Tweet Stream

This is a small class that utilizes the PHP Twitter Oauth library to quickly get and format tweet html.

##Basic Usage

```PHP
require_once('twitter-config.php');
require_once('twitter-display.php');
$tweets = new Tweet_Display("TyBruffy", API_KEY, API_SECRET, OAUTH_TOKEN, OAUTH_SECRET);
$tweets->display_tweets('<div class="tweet">%1$s</div>');
```
Pass a custom format string to the `display_tweets` method to return the tweet with your custom HTML.  Format strings should use the placeholder capabilities of [sprintf](http://www.php.net/manual/en/function.sprintf.php) to order the returned data.  All values are returned as strings, so for the 1st placeholder you would use `%1$s`  

| Placeholder # | Return Value |
|:-------------:|--------------|
| 1             | The tweet html.  Links and Hashtags are turned into html links and hashtags. |
| 2             | Direct URL to the tweet details. |
| 3             | A string containing the tweet time relative to the current time. |
| 4             | URL to link directly to reply to the tweet | 
| 5             | URL to link directly to retweet the tweet | 
| 6             | URL to link directly to favorite the tweet | 

## Advanced Usage
Nothing too fancy here, but you may also pass an integer to the `display_tweets` method to return multiple tweets.

```PHP
$tweets->display_tweets('<div class="tweet">%1$s</div>', 3);
```

##To Do
- [ ] Make `display_tweets` method return instead of echo.
- [ ] Add option for unfiltered tweet string.
- [ ] Add option for unfiltered tweet time.
