<?php defined( 'ABSPATH' ) or exit;

$posts_args = array(
	'post_type' 		=> array('post', 'project'),
	'post_status'   	=> 'publish',
	'posts_per_page' 	=> 500,
);

$yaturbo_rss = new WP_Query( $posts_args );

if ( $yaturbo_rss->have_posts() ) :

header('Content-Type: ' . feed_content_type('rss') . '; charset=' . get_option('blog_charset'), true);
echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?>';
?>
<rss xmlns:yandex="http://news.yandex.ru" xmlns:media="http://search.yahoo.com/mrss/" xmlns:turbo="http://turbo.yandex.ru" version="2.0">
<channel>
<title><?php bloginfo_rss('name'); ?></title>
<link><?php bloginfo_rss('url'); ?></link>
<description><?php bloginfo_rss("description"); ?></description>
<language>ru</language>
<yandex:analytics id="32330960" type="Yandex"></yandex:analytics>
<yandex:analytics id="UA-11031616-7" type="Google"></yandex:analytics>
<?php while( $yaturbo_rss->have_posts()) : $yaturbo_rss->the_post(); ?>
<item turbo="true">
<title><?php the_title_rss();?></title>
<link><?php the_permalink_rss();?></link>
<pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_post_time('Y-m-d H:i:s', true), false); ?></pubDate>
<author>Виталий Чалин</author>
<description><?php the_excerpt_rss();?></description>
<enclosure url="<?php the_post_thumbnail_url('full');?>" />
<turbo:content>
<![CDATA[
<?php
	$allowed_html = array(
			'figure', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'br', 'ul', 'ol', 'li', 'b', 'strong', 'i', 'em', 'sup',
			'sub', 'ins', 'del', 'small', 'big', 'pre', 'abbr', 'u', 'figcaption', 'video',

			'source' => array(
				'src' => true,
				'type' => true,
			),
			'a' => array(
				'href' => true,
			),
			'img' => array(
				'src' => true,
			)
		);
	$item_content = force_balance_tags( the_content() );
	$item_content = wp_kses( $item_content, $allowed_html);
	$item_content = preg_replace( '/^\s*\/\/<!\[CDATA\[([\s\S]*)\/\/\]\]>\s*\z/', '$1', $item_content );
	$item_content = preg_replace('/<!--(.|\s)*?-->/', '', $item_content);
	$item_content = preg_replace("/(<img\s(.+?)\/?>)/is", "<figure>$1</figure>", $item_content);
?>
<header>
<figure><img src="<?php the_post_thumbnail_url( 'thumb-900x450' ); ?>" /></figure>
<h1><?php the_title_rss();?></h1>
</header>
<?php echo $item_content; ?>
<div data-block="share" data-network="facebook, vkontakte, telegram, odnoklassniki, google, twitter"></div>
]]>
</turbo:content>
<?php
	$categories = chalinclub_get_terms_slug( $post->ID, 'category' );

    $args = array (
        'post_type'         => 'post',
        'category_name'     => $categories,
        'post__not_in'      => array($post->ID),
        'posts_per_page'	=> 2,
        'post_status'       => 'publish',
        'orderby'       	=> 'rand',
    );

    $rel_post = new WP_Query( $args );
    if ( $rel_post->have_posts() ) :
    	echo '<yandex:related>';
    	while ( $rel_post->have_posts() ) : $rel_post->the_post(); ?>
    		<link url="<?php the_permalink(); ?>" img="<?php the_post_thumbnail_url('thumb-450x225'); ?>"><?php the_title(); ?></link>
    	<?php endwhile;
    	echo '</yandex:related>';
    endif; wp_reset_postdata();
?>
</item>
<?php endwhile; ?>
</channel>
</rss>
<?php endif; wp_reset_postdata();