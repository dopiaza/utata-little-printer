<?php
/**
* Utata on Little Printer
*
* Author: David Wilkinson
* Web: http://dopiaza.org/
*
* Copyright (c) 2012 David Wilkinson
*
* Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated
* documentation files (the "Software"), to deal in the Software without restriction, including without limitation the
* rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to
* permit persons to whom the Software is furnished to do so, subject to the following conditions:
*
* The above copyright notice and this permission notice shall be included in all copies or substantial portions of
* the Software.
*
* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
* WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS
* OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
* OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*
*/

require_once dirname(__FILE__) . '/apikey.php';

spl_autoload_register(function($className)
{
	$className = str_replace ('\\', DIRECTORY_SEPARATOR, $className);
	include (dirname(__FILE__) . '/' . $className . '.php');
});

use \DPZ\Flickr;

// Generate the HTML for a post object obtained from Wordpress
function generateHTMLForPost($post)
{
	$postId = $post->ID;
	$photoId = getPhotoIdForPostId($postId);
	$author = get_userdata($post->post_author);

	// Gather together the useful bits and bobs that we want to appear in our publication
	$publication = array(
		'photoUrl' => getPhotoUrlForPostId($postId, $photoId),
		'title' => $post->post_title,
		'content' => apply_filters('the_content', $post->post_content),
		'author' => $author->display_name,
		'photographer' => getPhotographerForPostId($postId, $photoId),
	);
	
	// We use the post id as the etag to make sure that the subscriber doesn't get the same article twice
	header("ETag: $postId");
	
	// And include the file that generates the publication itself
	include dirname(__FILE__) . '/content.php';
}

// In Utata, the Flickr photo id for the photo that accompanies the post is held in a custom field 
// in the Wordpress post
function getPhotoIdForPostId($postId)
{
	$photoId = get_post_meta($postId, 'photoId', true);
	
	if (empty($photoId))
	{
		die("No photo specified for post $postId");
	}	
	return $photoId;
}

// Getting the Flickr URL for the photo requires a Flickr API call. Rather than do that each time this edition
// is printed, we'll cache that information in another custom field in the post.
function getPhotoUrlForPostId($postId, $photoId)
{
	$photoUrl = get_post_meta($postId, 'bergPhotoUrl', true);
	
	if (empty($photoUrl))
	{
		getPhotoInfoForPostId($postId, $photoId);
		$photoUrl = get_post_meta($postId, 'bergPhotoUrl', true);
	}	
	
	return $photoUrl;
}

// Similarly the name of the photographer is retrieved via the Flickr API, so we cache that information
// in another custom field.
function getPhotographerForPostId($postId, $photoId)
{
	$photographer = get_post_meta($postId, 'bergPhotographer', true);

	if (empty($photographer))
	{
		getPhotoInfoForPostId($postId, $photoId);
		$photographer = get_post_meta($postId, 'bergPhotographer', true);
	}	

	return $photographer;
}

// Retrieve details for the photo from Flickr
function getPhotoInfoForPostId($postId, $photoId)
{
	global $flickrApiKey;

	$flickr = new Flickr($flickrApiKey);

	$parameters =  array(
		'photo_id' => $photoId,
	);

	$response = $flickr->call('flickr.photos.getInfo', $parameters);

	$photo = @$response['photo'];
	
	if (empty($photo))
	{
		die("Cannot get photo info from Flickr (Photo ID: $photoId)");
	}
	
	$secret = @$photo['secret'];
	$server = @$photo['server'];
	$farm = @$photo['farm'];

	$photoUrl = sprintf('http://farm%s.staticflickr.com/%s/%s_%s.jpg', $farm, $server, $photoId, $secret);
	add_post_meta($postId, 'bergPhotoUrl', $photoUrl, true);
	
	$name = @$photo['owner']['realname'];
	if (empty($name))
	{
		$name = @$photo['owner']['username'];	
	}
	add_post_meta($postId, 'bergPhotographer', $name, true);
}

// Retrieve the photo from Flickr, convert to greyscale and size correctly.
function getLocalPhoto($remoteUrl)
{
	$dir = dirname(__FILE__) . '/images';
	$filename = basename($remoteUrl);
	$original = "$dir/original/$filename";
	$converted = "$dir/converted/$filename";

	// If any of this process fails for any reason, just use the Flickr URL directly
	$useUrl = $remoteUrl;
	$convertedUrl = "http://www.utata.org/berg/images/converted/$filename";

	if (file_exists($converted) && filesize($converted) > 0)
	{
		// We've already successfully retrieved and converted the image, so we'll use that one
		$useUrl = $convertedUrl;
	}
	else
	{
		// Fetch and convert
		$fp = fopen($original, 'w');

		$ch = curl_init($remoteUrl);
		curl_setopt($ch, CURLOPT_FILE, $fp);

		$data = curl_exec($ch);

		curl_close($ch);
		fclose($fp);

		if (file_exists($original) && filesize($original) > 0)
		{
			// This calls a simple shell script that invokes ImageMagick to convert to greyscale and resize the image.
			// If you have the Imagick PHP extension installed, you could replace this line with a call to that,
			// which would undoubtedly be more efficient.
			system("/bin/sh " . dirname(__FILE__) . "/convert.sh > /dev/null $original $converted  2>&1");
			
			if (file_exists($converted) && filesize($converted) > 0)
			{
				$useUrl = $convertedUrl;
			}
		}		
	}

	return $useUrl;
}

