=== Latest Tweets ===
Contributors: colinbm
Tags: twitter
Requires at least: 3.0.1
Tested up to: 3.7.1
Stable tag:
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Access the latest tweets from anywhere (user timeline, list, followers, etc).

== Description ==

Once configured using the Twitter API credentials, the default behaviour is to return the tweets from the authenticated user account.

The behaviour can be changed by specifying a method representing an API call, as detailed in the CodeBird docs - https://github.com/jublonet/codebird-php#3-mapping-api-methods-to-codebird-function-calls

When an API call requires parameters, these can be specified in the Parameters field, so e.g.:

    Method: lists_statuses
    Parameters: slug=my_list_name&owner_screen_name=my_screen_name

