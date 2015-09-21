<?php

class SampleTest extends WP_UnitTestCase {

	function tearDown() {
		// Remove all uploads.
		$this->remove_added_uploads();
		parent::tearDown();
	}

	/**
	 * Helper function that creates an attachment in the DB.
	 * Copied from Tests_Post_Attachments Class in the WP Core test suite.
	 */
	private function _make_attachment( $upload, $parent_post_id = 0 ) {

		$type = '';
		if ( !empty($upload['type']) ) {
			$type = $upload['type'];
		} else {
			$mime = wp_check_filetype( $upload['file'] );
			if ($mime)
				$type = $mime['type'];
		}

		$attachment = array(
			'post_title' => basename( $upload['file'] ),
			'post_content' => '',
			'post_type' => 'attachment',
			'post_parent' => $parent_post_id,
			'post_mime_type' => $type,
			'guid' => $upload[ 'url' ],
		);

		// Save the data
		$id = wp_insert_attachment( $attachment, $upload[ 'file' ], $parent_post_id );
		wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $upload['file'] ) );

		return $this->ids[] = $id;

	}

	/**
	 * Helper function to create an attachment from a file
	 *
	 * @uses _make_attachment
	 *
	 * @param 	string 			Optional. A path to a file. Default: DIR_TESTDATA.'/images/canola.JPG'.
	 * @return 	int|bool 		An attachment ID or false.
	 */
	private function _test_img( $file = null ) {

		$filename = $file ? $file : ( dirname(__FILE__) . '/data/test-large.png' );
		$contents = file_get_contents($filename);

		$upload = wp_upload_bits(basename($filename), null, $contents);
		$this->assertTrue( empty($upload['error']) );

		$id = $this->_make_attachment($upload);

		return $id;
	}

	/* OUR TESTS */

	function test_tevkori_get_sizes() {
		// make an image
		$id = $this->_test_img();

		global $content_width;

		// test sizes against the default WP sizes
		$intermediates = array('thumbnail', 'medium', 'large');

		foreach( $intermediates as $int ) {
			$width = get_option( $int . '_size_w' );

			// the sizes width gets constrained to $content_width by default
			if ( $content_width > 0 ) {
				$width = ( $width > $content_width ) ? $content_width : $width;
			}

			$expected = '(max-width: ' . $width . 'px) 100vw, ' . $width . 'px';
			$sizes = tevkori_get_sizes( $id, $int );

			$this->assertSame($expected, $sizes);
		}
	}

	function test_tevkori_get_sizes_with_args() {
		// make an image
		$id = $this->_test_img();

		$args = array(
			'sizes' => array(
				array(
					'size_value' 	=> '10em',
					'mq_value'		=> '60em',
					'mq_name'			=> 'min-width'
				),
				array(
					'size_value' 	=> '20em',
					'mq_value'		=> '30em',
					'mq_name'			=> 'min-width'
				),
				array(
					'size_value'	=> 'calc(100vm - 30px)'
				),
			)
		);

		$expected = '(min-width: 60em) 10em, (min-width: 30em) 20em, calc(100vm - 30px)';
		$sizes = tevkori_get_sizes( $id, 'medium', $args );

		$this->assertSame($expected, $sizes);
	}

	function test_filter_tevkori_get_sizes_string() {
		// Add our test filter.
		add_filter( 'tevkori_image_sizes_args', array( $this, '_test_tevkori_image_sizes_args' ) );

		// Set up our test.
		$id = $this->_test_img();
		$sizes = tevkori_get_sizes($id, 'medium');

		// Evaluate that the sizes returned is what we expected.
		$this->assertSame( $sizes, '100vm');

		remove_filter( 'tevkori_image_sizes_args', array( $this, '_test_tevkori_image_sizes_args' ) );
	}

	/**
	 * A simple test filter for tevkori_get_sizes().
	 */
	function _test_tevkori_image_sizes_args( $args ) {
		$args['sizes'] = "100vm";
		return $args;
	}

	function test_filter_tevkori_srcset_array() {
		// Add test filter
		add_filter( 'tevkori_srcset_array', array( $this, '_test_tevkori_srcset_array' ) );

		// Set up our test.
		$id = $this->_test_img();
		$sizes = tevkori_get_srcset_array($id, 'medium');

		// Evaluate that the sizes returned is what we expected.
		foreach( $sizes as $width => $source ) {
			$this->assertTrue( $width <= 500 );
		}

		// Remove test filter
		remove_filter( 'tevkori_srcset_array', array( $this, '_test_tevkori_srcset_array' ) );
	}

	/**
	 * A test filter for tevkori_get_srcset_array() that removes any sources
	 * that are larger that 500px wide.
	 */
	function _test_tevkori_srcset_array( $array ) {
		foreach ( $array as $size => $file ) {
			if ( $size > 500 ) {
				unset( $array[$size] );
			}
		}
		return $array;
	}

	function test_tevkori_get_sizes_string() {
		// make an image
		$id = $this->_test_img();

		$sizes = tevkori_get_sizes($id, 'medium');
		$sizes_string = tevkori_get_sizes_string( $id, 'medium' );

		$expected = 'sizes="' . $sizes . '"';

		$this->assertSame( $expected, $sizes_string);
	}

	function test_tevkori_get_srcset_array() {
		// make an image
		$id = $this->_test_img();
		$sizes = tevkori_get_srcset_array( $id, 'medium' );

		$year_month = date('Y/m');
		$image = wp_get_attachment_metadata( $id );

		$expected = array(
			$image['sizes']['medium']['width'] => 'http://example.org/wp-content/uploads/' . $year_month = date('Y/m') . '/'
				. $image['sizes']['medium']['file'] . ' ' . $image['sizes']['medium']['width'] . 'w',
			$image['sizes']['large']['width'] => 'http://example.org/wp-content/uploads/' . $year_month = date('Y/m') . '/'
				. $image['sizes']['large']['file'] . ' ' . $image['sizes']['large']['width'] . 'w',
			$image['width'] => 'http://example.org/wp-content/uploads/' . $image['file'] . ' ' . $image['width'] .'w'
		);

		$this->assertSame( $expected, $sizes );
	}

	function test_tevkori_get_srcset_array_no_date_upoads() {
		// Save the current setting for uploads folders
		$uploads_use_yearmonth_folders = get_option( 'uploads_use_yearmonth_folders' );

		// Disable date organized uploads
		update_option( 'uploads_use_yearmonth_folders', 0 );

		// make an image
		$id = $this->_test_img();
		$sizes = tevkori_get_srcset_array( $id, 'medium' );

		$image = wp_get_attachment_metadata( $id );

		$expected = array(
			$image['sizes']['medium']['width'] => 'http://example.org/wp-content/uploads/' . $image['sizes']['medium']['file'] . ' ' . $image['sizes']['medium']['width'] . 'w',
			$image['sizes']['large']['width'] => 'http://example.org/wp-content/uploads/' . $image['sizes']['large']['file'] . ' ' . $image['sizes']['large']['width'] . 'w',
			$image['width'] => 'http://example.org/wp-content/uploads/' . $image['file'] . ' ' . $image['width'] .'w'
		);

		$this->assertSame( $expected, $sizes );

		// Leave the uploads option the way you found it.
		update_option( 'uploads_use_yearmonth_folders', $uploads_use_yearmonth_folders );
	}

	function test_tevkori_get_srcset_array_single_srcset() {
		// make an image
		$id = $this->_test_img();
		// In our tests, thumbnails would only return a single srcset candidate,
		// in which case we don't bother returning a srcset array.
		$sizes = tevkori_get_srcset_array( $id, 'thumbnail' );

		$this->assertFalse( $sizes );
	}

	/**
	 * Test for filtering out leftover sizes after an image is edited.
	 * @group 155
	 */
	function test_tevkori_get_srcset_array_with_edits() {
		// Make an image.
		$id = $this->_test_img();

		// For this test we're going to mock metadata changes from an edit.
		// Start by getting the attachment metadata.
		$meta = wp_get_attachment_metadata( $id );

		// Mimick hash generation method used in wp_save_image().
		$hash = 'e' . time() . rand(100, 999);

		// Replace file paths for full and medium sizes with hashed versions.
		$filename_base = basename( $meta['file'], '.png' );
		$meta['file'] = str_replace( $filename_base, $filename_base . '-' . $hash, $meta['file'] );
		$meta['sizes']['medium']['file'] = str_replace( $filename_base, $filename_base . '-' . $hash, $meta['sizes']['medium']['file'] );

		// Save edited metadata.
		wp_update_attachment_metadata( $id, $meta );

		// Get the edited image and observe that a hash was created.
		$img_url = wp_get_attachment_url( $id );

		// Calculate a srcset array.
		$sizes = tevkori_get_srcset_array( $id, 'medium' );

		// Test to confirm all sources in the array include the same edit hash.
		foreach ( $sizes as $size ) {
			$this->assertTrue( false !== strpos( $size, $hash ) );
		}
	}

	function test_tevkori_get_srcset_array_false() {
		// make an image
		$id = $this->_test_img();
		$sizes = tevkori_get_srcset_array( 99999, 'foo' );

		// For canola.jpg we should return
		$this->assertFalse( $sizes );
	}

	function test_tevkori_get_srcset_array_no_width() {
		// Filter image_downsize() output.
		add_filter( 'image_downsize', array( $this, '_test_tevkori_get_srcset_array_no_width_filter' ) );

		// Make our attachement.
		$id = $this->_test_img();
		$srcset = tevkori_get_srcset_array( $id, 'medium' );

		// The srcset should be false
		$this->assertFalse( $srcset );

		// Remove filter.
		remove_filter( 'image_downsize', array( $this, '_test_tevkori_get_srcset_array_no_width_filter' ) );
	}

	/**
	 * Helper funtion to filter image_downsize and return zero values for width and height.
	 */
	public function _test_tevkori_get_srcset_array_no_width_filter() {
		return array( 'http://example.org/foo.jpg', 0, 0, false );
	}

	function test_tevkori_get_srcset_string() {
		// make an image
		$id = $this->_test_img();
		$sizes = tevkori_get_srcset_string( $id, 'full-size' );

		$image = wp_get_attachment_metadata( $id );
		$year_month = date('Y/m');

		$expected = 'srcset="';
		$expected .= 'http://example.org/wp-content/uploads/' . $year_month = date('Y/m') . '/'
			. $image['sizes']['medium']['file'] . ' ' . $image['sizes']['medium']['width'] . 'w, ';
		$expected .='http://example.org/wp-content/uploads/' . $year_month = date('Y/m') . '/'
			. $image['sizes']['large']['file'] . ' ' . $image['sizes']['large']['width'] . 'w, ';
		$expected .= 'http://example.org/wp-content/uploads/' . $image['file'] . ' ' . $image['width'] .'w"';

		$this->assertSame( $expected, $sizes );
	}

}
