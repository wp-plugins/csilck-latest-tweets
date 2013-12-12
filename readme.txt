=== Latest Tweets ===
Contributors: colinbm
Tags: twitter
Requires at least: 3.0.1
Tested up to: 3.7.1
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==

Access the latest tweets from anywhere (user timeline, list, followers, etc).

== Installation ==

Once configured using the Twitter API credentials, the default behaviour is to return the tweets from the authenticated user account.

The behaviour can be changed by specifying a method representing an API call, as detailed in the CodeBird docs - https://github.com/jublonet/codebird-php#3-mapping-api-methods-to-codebird-function-calls

When an API call requires parameters, these can be specified in the Parameters field, so e.g.:

    Method: lists_statuses
    Parameters: slug=my_list_name&owner_screen_name=my_screen_name

Display is left up to you. In your template you can do something like:

    <ul>
      <?php foreach (Latest_Tweets::range(1) as $tweet): ?>
        <li>
          <strong>
            <?php echo $tweet->user->name ?>
            <span><a href="http://twitter.com/<?php echo $tweet->user->screen_name ?>"><?php echo $tweet->user->screen_name ?></a></span>
          </strong>
          <p><?php echo Latest_Tweets::highlight($tweet->text) ?></p>
        </li>
      <? endforeach ?>
    </ul>

You can also get just one tweet with:

    Latest_Tweet::one()
