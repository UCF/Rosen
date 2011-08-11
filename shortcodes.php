<?php

/**
 * Create a javascript slideshow of each top level element in the
 * shortcode.  All attributes are optional, but may default to less than ideal
 * values.  Available attributes:
 * 
 * height     => css height of the outputted slideshow, ex. height="100px"
 * width      => css width of the outputted slideshow, ex. width="100%"
 * transition => length of transition in milliseconds, ex. transition="1000"
 * cycle      => length of each cycle in milliseconds, ex cycle="5000"
 *
 * Example:
 * [slideshow height="500px" transition="500" cycle="2000"]
 * <img src="http://some.image.com" .../>
 * <div class="robots">Robots are coming!</div>
 * <p>I'm a slide!</p>
 * [/slideshow]
 **/
function sc_slideshow($attr, $content=null){
	$content = cleanup(str_replace('<br />', '', $content));
	$content = DOMDocument::loadHTML($content);
	$html    = $content->childNodes->item(1);
	$body    = $html->childNodes->item(0);
	$content = $body->childNodes;
	
	# Find top level elements and add appropriate class
	$items = array();
	foreach($content as $item){
		if ($item->nodeName != '#text'){
			$classes   = explode(' ', $item->getAttribute('class'));
			$classes[] = 'slide';
			$item->setAttribute('class', implode(' ', $classes));
			$items[] = $item->ownerDocument->saveXML($item);
		}
	}
	
	$height    = ($attr['height']) ? $attr['height'] : '100px';
	$width     = ($attr['width']) ? $attr['width'] : '100%';
	$tran_len  = ($attr['transition']) ? $attr['transition'] : 1000;
	$cycle_len = ($attr['cycle']) ? $attr['cycle'] : 5000;
	
	ob_start();
	?>
	<div 
		class="slideshow"
		data-tranlen="<?=$tran_len?>"
		data-cyclelen="<?=$cycle_len?>"
		style="height: <?=$height?>; width: <?=$width?>;"
	>
		<?php foreach($items as $item):?>
		<?=$item?>
		<?php endforeach;?>
	</div>
	<?php
	$html = ob_get_clean();
	
	return $html;
}
add_shortcode('slideshow', 'sc_slideshow');

/**
 * Build gallery from Flickr RSS feed
 *
 * @return string
 * @author Chris Conover
 **/
function sc_flickr_gallery($atts = Array())
{
	$num_photos = (isset($atts['num_photos']) && is_int($atts['num_photos'])) ? $atts['num_photos'] : 25;
	$feed_url = get_theme_option('gallery_feed_url');
	
	if($feed_url !== False) {
		$rss = fetch_feed($feed_url);
		if(!is_wp_error($rss)) {
			$items = $rss->get_items(0, $rss->get_item_quantity($num_photos));
			$photos = Array();
			foreach($items as $item) {
				if(preg_match('/<img([^>]+)>/', $item->get_description(), $matches) == 1) {
					$img_atts   = $matches[1];
					$img_src    = null;
					$img_width  = null;
					$img_height = null;
					
					if(preg_match('/src="([^"]+)"/', $img_atts, $matches) == 1) {
						$img_src = $matches[1];
					}
					if(preg_match('/width="(\d+)"/', $img_atts, $matches) == 1) {
						$img_width = $matches[1];
					}
					if(preg_match('/height="(\d+)"/', $img_atts, $matches) == 1) {
						$img_height = $matches[1];
					}
					if(!is_null($img_src) && !is_null($img_width) && !is_null($img_height)) {
						array_push($photos, array('src'    => $img_src, 
																			'width'  => $img_width, 
																			'height' => $img_height,
																			'link'   => $item->get_link()));
					}
				}
			}
		}
		$count = 1;
		ob_start();?>
		<ul id="flickr_gallery">
			<?foreach($photos as $photo) {?>
				<li class="<?=((($count % 7) == 0) ? 'last' :'')?>">
					<a href="<?=substr($photo['src'], 0, strlen($photo['src']) - 6).'_z.jpg'?>">
						<img src="<?=substr($photo['src'], 0, strlen($photo['src']) - 6).'_s.jpg'?>" />
					</a>
				</li>
			<?$count++;
			}?>
		</ul>
		<?
		return ob_get_clean();
	}
}
add_shortcode('flickr-gallery', 'sc_flickr_gallery');
?>