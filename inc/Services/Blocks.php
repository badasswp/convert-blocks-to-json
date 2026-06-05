<?php
/**
 * Blocks Service.
 *
 * This service is responsible for binding Block
 * import customizations to WordPress.
 *
 * @package ConvertBlocksToJSON
 */

namespace ConvertBlocksToJSON\Services;

use ConvertBlocksToJSON\Blocks\Audio;
use ConvertBlocksToJSON\Blocks\Accordion;
use ConvertBlocksToJSON\Blocks\Details;
use ConvertBlocksToJSON\Blocks\Footnotes;
use ConvertBlocksToJSON\Blocks\Freeform;
use ConvertBlocksToJSON\Blocks\Heading;
use ConvertBlocksToJSON\Blocks\Image;
use ConvertBlocksToJSON\Blocks\Lists;
use ConvertBlocksToJSON\Blocks\ListItem;
use ConvertBlocksToJSON\Blocks\MediaText;
use ConvertBlocksToJSON\Blocks\Paragraph;
use ConvertBlocksToJSON\Blocks\Pullquote;
use ConvertBlocksToJSON\Blocks\Table;
use ConvertBlocksToJSON\Blocks\Video;

use ConvertBlocksToJSON\Abstracts\Block;
use ConvertBlocksToJSON\Abstracts\Service;
use ConvertBlocksToJSON\Interfaces\Kernel;

class Blocks extends Service implements Kernel {
	/**
	 * Blocks classes.
	 *
	 * @since 1.2.0
	 *
	 * @var Block[]
	 */
	public array $blocks;

	/**
	 * Set up.
	 *
	 * @since 1.2.0
	 *
	 * @return void
	 */
	public function __construct() {
		$this->blocks = [
			Audio::class,
			Accordion::class,
			Details::class,
			Footnotes::class,
			Freeform::class,
			Heading::class,
			Image::class,
			Lists::class,
			ListItem::class,
			MediaText::class,
			Paragraph::class,
			Pullquote::class,
			Table::class,
			Video::class,
		];
	}

	/**
	 * Bind to WP.
	 *
	 * @since 1.2.0
	 *
	 * @return void
	 */
	public function register(): void {
		/**
		 * Run `init` to filter Block types.
		 *
		 * @since 1.2.0
		 *
		 * @var Block $block
		 */
		foreach ( $this->get_blocks() as $block ) {
			$block->init();
		}
	}

	public function get_blocks(): array {
		/**
		 * Filter Block classes.
		 *
		 * This provides a way to filter the block
		 * classes before they are called.
		 *
		 * @since 1.2.0
		 *
		 * @param Block[] $blocks Block classes.
		 * @return Block[]
		 */
		$blocks = apply_filters( 'cbtj_blocks', $this->blocks );

		return array_map( fn( $block ) => new $block(), $blocks );
	}
}
