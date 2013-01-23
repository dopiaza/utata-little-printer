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

// Load up the Wordpress libraries. This assumes that the wordpress directory is alongside this one. Adjust the 
// path to suit your particular setup
require_once dirname(__FILE__) . '/../wordpress/wp-load.php';


// And load up the code to generate the publication
require_once dirname(__FILE__) . '/util.php';

// Utata is a multi-site blog, so we need to switch to the correct blog first. If you have a standard single-site
// Wordpress installation, you should delete this line
switch_to_blog(2);

// We want the latest post from the blog
$args = array( 
	'numberposts' 	=> 1, 
	'orderby' 		=> 'post_date',
	'order'         => 'DESC',
 );
 
// Get the post details from Wordpress
$posts = get_posts($args);

if (!empty($posts))
{
	// And generate the content
	$post = $posts[0];
	generateHTMLForPost($post);
}
else
{
	die("Couldn't get latest post");
}
