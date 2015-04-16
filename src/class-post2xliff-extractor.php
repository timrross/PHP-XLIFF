<?php
class Post2Xliff_Extractor {
	public static function extract( $post ) {
		if ( ! $post instanceof WP_Post ) {
			return false;
		}

		$translatable_content = self::get_translatable_content_from_post( $post );

		$xliff = new Xliff_Document();
		$xliff->set_source_locale( get_locale() );
		$xliff->set_target_locale( '%%%%trgLang%%%%');
		$xliff->file(true)->set_attribute( 'id', $post->ID );

		foreach ( $translatable_content as $content_type => $content ) {
			$xliff->file()->unit( true );
			$xliff->file()->unit()->set_attribute( 'id', $content_type );

			switch( $content_type ) {
				case 'content':
				case 'excerpt':
				case 'slug':
				case 'title':
					$xliff->file()->unit()->segment(true)->source(true)->set_text_content( $content );
					$xliff->file()->unit()->segment()->target(true);
					break;
				case 'categories':
				case 'tags':
					if ( ! is_array( $content ) ) {
						$content = array();
					}
					foreach ( $content as $tag ) {
						$xliff->file()->unit()->segment(true)->source(true)->set_text_content( $tag );
						$xliff->file()->unit()->segment()->target(true);
					}
					break;
			}
		}

		return $xliff;
	}

	public static function get_translatable_content_from_post( $post ) {
		$translatable_content = array(
					'categories'  => wp_get_post_categories( $post->ID, array( 'fields' => 'names' ) ),
					'content'     => $post->post_content,
					'excerpt'     => $post->post_excerpt,
					'slug'        => $post->post_name,
					'tags'        => wp_get_post_tags( $post->ID, array( 'fields' => 'names' ) ),
					'title'       => $post->post_title
				);
		return array_diff( $translatable_content, array( '' ) );
	}
}