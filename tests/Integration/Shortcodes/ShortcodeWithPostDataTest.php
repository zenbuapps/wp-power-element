<?php
/**
 * 短碼搭配真實 WordPress Post 資料的整合測試
 * 驗證短碼在 WP 資料庫互動情境下的行為
 */

declare( strict_types=1 );

namespace Tests\Integration\Shortcodes;

use J7\PowerElement\Shortcodes\InteractiveImage\InteractiveImage;
use Tests\Integration\TestCase;

/**
 * Class ShortcodeWithPostDataTest
 *
 * @group happy
 * @group error
 * @group edge
 */
class ShortcodeWithPostDataTest extends TestCase {

	// ========== 快樂路徑（Happy Flow）==========

	/**
	 * @test
	 * @group happy
	 */
	public function 短碼_get_info_回傳正確資訊_短碼名稱應為pe_interactive_image(): void {
		// Given：InteractiveImage 類別
		// When：呼叫 get_info()
		// Then：第一個元素應為 'pe_interactive_image'
		[ $name ] = InteractiveImage::get_info();
		$this->assertSame( 'pe_interactive_image', $name );
	}

	/**
	 * @test
	 * @group happy
	 */
	public function 短碼_get_info_回傳正確資訊_標籤應為互動圖片(): void {
		// Given：InteractiveImage 類別
		// When：呼叫 get_info()
		// Then：第二個元素應為 '互動圖片'
		[, $label ] = InteractiveImage::get_info();
		$this->assertSame( '互動圖片', $label );
	}

	/**
	 * @test
	 * @group happy
	 */
	public function 短碼_實作IShortcode介面_應有callback靜態方法(): void {
		// Given：InteractiveImage 類別
		// When：透過反射檢查介面實作
		// Then：應實作 IShortcode，callback 應為 public static
		$reflection = new \ReflectionClass( InteractiveImage::class );
		$this->assertTrue(
			$reflection->implementsInterface(
				'J7\PowerElement\Shortcodes\Shared\Interfaces\IShortcode'
			),
			'InteractiveImage 應實作 IShortcode 介面'
		);

		$callback_method = $reflection->getMethod( 'callback' );
		$this->assertTrue( $callback_method->isPublic(), 'callback() 應為 public' );
		$this->assertTrue( $callback_method->isStatic(), 'callback() 應為 static' );
	}

	/**
	 * @test
	 * @group happy
	 */
	public function 短碼_實作IShortcode介面_應有get_info靜態方法(): void {
		// Given：InteractiveImage 類別
		// When：透過反射檢查方法
		// Then：get_info 應為 public static
		$reflection = new \ReflectionClass( InteractiveImage::class );
		$get_info   = $reflection->getMethod( 'get_info' );
		$this->assertTrue( $get_info->isPublic(), 'get_info() 應為 public' );
		$this->assertTrue( $get_info->isStatic(), 'get_info() 應為 static' );
	}

	/**
	 * @test
	 * @group happy
	 */
	public function 短碼_在WP環境中_呼叫wp_get_attachment_image_url不存在的id_應不拋出例外(): void {
		// Given：附件 ID 135 可能不存在於測試資料庫
		// When：短碼 callback 內部會呼叫 wp_get_attachment_image_url(135, 'full')
		// Then：即使找不到附件，應回傳字串（不拋出例外）
		$threw = false;
		try {
			$output = InteractiveImage::callback( [] );
			$this->assertIsString( $output );
		} catch ( \Throwable $e ) {
			$threw           = true;
			$this->lastError = $e;
		}
		$this->assertFalse( $threw, '附件不存在時短碼不應拋出例外' );
	}

	// ========== 錯誤處理（Error Handling）==========

	/**
	 * @test
	 * @group error
	 */
	public function 短碼_callback_傳入不相關的多餘屬性_應忽略多餘屬性(): void {
		// Given：包含不相關屬性的 atts 陣列
		// When：呼叫 callback
		// Then：應正常執行，不拋出例外
		$threw = false;
		try {
			$output = InteractiveImage::callback( [
				'unknown_key'  => 'unknown_value',
				'another_key'  => 'another_value',
				'number_key'   => 99999,
			] );
			$this->assertIsString( $output );
		} catch ( \Throwable $e ) {
			$threw           = true;
			$this->lastError = $e;
		}
		$this->assertFalse( $threw, '多餘屬性不應造成例外' );
	}

	// ========== 邊緣案例（Edge Cases）==========

	/**
	 * @test
	 * @group edge
	 */
	public function 短碼_Register_register_hooks_應冪等(): void {
		// Given：Shortcodes\Register::register_hooks() 已被 Bootstrap 呼叫
		// When：再次手動呼叫
		// Then：short code 仍應登記，不拋出例外
		$threw = false;
		try {
			\J7\PowerElement\Shortcodes\Register::register_hooks();
		} catch ( \Throwable $e ) {
			$threw           = true;
			$this->lastError = $e;
		}
		$this->assertFalse( $threw, '多次呼叫 register_hooks 不應拋出例外' );
		$this->assert_shortcode_registered( 'pe_interactive_image' );
	}

	/**
	 * @test
	 * @group edge
	 */
	public function 短碼_巢狀在wp_文章內容中_應正確解析(): void {
		// Given：建立一篇含短碼的文章
		// When：取得文章內容並執行 do_shortcode
		// Then：短碼應被解析
		$post_id = $this->create_post( [
			'post_content' => '[pe_interactive_image]',
		] );
		$post    = get_post( $post_id );
		$this->assertNotNull( $post );

		$rendered = do_shortcode( $post->post_content );
		$this->assertStringNotContainsString(
			'[pe_interactive_image]',
			$rendered,
			'文章內容中的短碼應被解析'
		);
	}

	/**
	 * @test
	 * @group edge
	 */
	public function 短碼_在WP_the_content_filter中_應被正確解析(): void {
		// Given：建立一篇含短碼的文章
		// When：透過 apply_filters('the_content') 模擬前台輸出
		// Then：短碼應被解析（WordPress 內建 the_content filter 會呼叫 do_shortcode）
		$post_id = $this->create_post( [
			'post_content' => '[pe_interactive_image]',
		] );
		$post    = get_post( $post_id );
		$this->assertNotNull( $post );

		$filtered = apply_filters( 'the_content', $post->post_content );
		$this->assertStringNotContainsString(
			'[pe_interactive_image]',
			$filtered,
			'the_content filter 應解析短碼'
		);
	}
}
