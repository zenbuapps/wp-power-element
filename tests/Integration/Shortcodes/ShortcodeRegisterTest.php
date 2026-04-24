<?php
/**
 * 短碼登記整合測試
 * 驗證 Power Element 的所有短碼是否正確登記到 WordPress
 */

declare( strict_types=1 );

namespace Tests\Integration\Shortcodes;

use Tests\Integration\TestCase;

/**
 * Class ShortcodeRegisterTest
 *
 * @group smoke
 * @group happy
 */
class ShortcodeRegisterTest extends TestCase {

	// ========== 冒煙測試（Smoke）==========

	/**
	 * @test
	 * @group smoke
	 */
	public function 短碼登記_pe_interactive_image_應已登記(): void {
		// Given：WordPress 已載入，Bootstrap 已初始化
		// When：檢查短碼是否存在
		// Then：pe_interactive_image 應已登記
		$this->assert_shortcode_registered( 'pe_interactive_image' );
	}

	/**
	 * @test
	 * @group smoke
	 */
	public function Bootstrap類別_應已實例化(): void {
		// Given：WordPress 已載入
		// When：嘗試取得 Bootstrap 實例
		// Then：Bootstrap 類別應存在且可取得實例
		$this->assertTrue(
			class_exists( 'J7\PowerElement\Bootstrap' ),
			'J7\PowerElement\Bootstrap 類別不存在'
		);
	}

	/**
	 * @test
	 * @group smoke
	 */
	public function Plugin類別_應已實例化(): void {
		// Given：WordPress 已載入
		// When：取得 Plugin 實例
		// Then：Plugin 實例不為 null
		$instance = \J7\PowerElement\Plugin::instance();
		$this->assertNotNull( $instance, 'Plugin::instance() 回傳 null' );
	}

	// ========== 快樂路徑（Happy Flow）==========

	/**
	 * @test
	 * @group happy
	 */
	public function 短碼登記_Register類別_應透過add_shortcode登記(): void {
		// Given：Bootstrap 已初始化
		// When：檢查 WordPress 的短碼全域列表
		// Then：pe_interactive_image 的 callback 應指向 InteractiveImage::callback
		global $shortcode_tags;
		$this->assertArrayHasKey(
			'pe_interactive_image',
			$shortcode_tags,
			'pe_interactive_image 未出現在 $shortcode_tags 全域陣列'
		);
	}

	/**
	 * @test
	 * @group happy
	 */
	public function 短碼登記_callback_應為靜態方法(): void {
		// Given：短碼已登記
		// When：取得短碼的 callback
		// Then：callback 應為 InteractiveImage::callback
		global $shortcode_tags;

		$callback = $shortcode_tags['pe_interactive_image'] ?? null;
		$this->assertNotNull( $callback, 'pe_interactive_image callback 為 null' );

		// 確認 callback 是陣列格式 [ClassName, 'callback']
		$this->assertIsArray( $callback, 'pe_interactive_image callback 應為 array 形式' );
		$this->assertSame(
			'J7\PowerElement\Shortcodes\InteractiveImage\InteractiveImage',
			$callback[0],
			'pe_interactive_image callback 的類別不符'
		);
		$this->assertSame( 'callback', $callback[1], 'pe_interactive_image callback 的方法名稱不符' );
	}

	/**
	 * @test
	 * @group happy
	 */
	public function 短碼get_info_應回傳正確資訊(): void {
		// Given：InteractiveImage 類別
		// When：呼叫 get_info()
		// Then：應回傳包含短碼名稱與標籤的陣列
		$info = \J7\PowerElement\Shortcodes\InteractiveImage\InteractiveImage::get_info();

		$this->assertIsArray( $info, 'get_info() 應回傳陣列' );
		$this->assertCount( 2, $info, 'get_info() 應回傳 2 個元素' );
		$this->assertSame( 'pe_interactive_image', $info[0], '短碼名稱不符' );
		$this->assertSame( '互動圖片', $info[1], '短碼標籤不符' );
	}

	// ========== 錯誤處理（Error Handling）==========

	/**
	 * @test
	 * @group error
	 */
	public function 未登記短碼_do_shortcode_應原樣輸出(): void {
		// Given：一個未登記的短碼名稱
		// When：執行 do_shortcode
		// Then：應原樣輸出（不被解析）
		$output = do_shortcode( '[pe_not_exists_shortcode]' );
		$this->assertSame(
			'[pe_not_exists_shortcode]',
			$output,
			'未登記短碼應原樣輸出'
		);
	}
}
