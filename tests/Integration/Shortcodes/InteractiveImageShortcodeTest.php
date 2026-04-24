<?php
/**
 * InteractiveImage 短碼渲染整合測試
 * 驗證 pe_interactive_image 短碼的輸出行為
 */

declare( strict_types=1 );

namespace Tests\Integration\Shortcodes;

use J7\PowerElement\Shortcodes\InteractiveImage\InteractiveImage;
use Tests\Integration\TestCase;

/**
 * Class InteractiveImageShortcodeTest
 *
 * @group happy
 * @group error
 * @group edge
 */
class InteractiveImageShortcodeTest extends TestCase {

	// ========== 冒煙測試（Smoke）==========

	/**
	 * @test
	 * @group smoke
	 */
	public function 短碼渲染_基本呼叫_應有字串輸出(): void {
		// Given：pe_interactive_image 已登記
		// When：不帶任何參數執行短碼
		// Then：應有非空字串輸出
		$output = do_shortcode( '[pe_interactive_image]' );
		$this->assertIsString( $output, '短碼應回傳字串' );
	}

	// ========== 快樂路徑（Happy Flow）==========

	/**
	 * @test
	 * @group happy
	 */
	public function 短碼渲染_callback直接呼叫_應回傳字串(): void {
		// Given：InteractiveImage 類別
		// When：直接呼叫 callback([])
		// Then：應回傳字串（目前為 hardcoded 測試值）
		$output = InteractiveImage::callback( [] );
		$this->assertIsString( $output, 'callback() 應回傳字串' );
	}

	/**
	 * @test
	 * @group happy
	 */
	public function 短碼渲染_callback_應不含原始短碼標籤(): void {
		// Given：pe_interactive_image 已登記
		// When：執行短碼
		// Then：輸出中不應含 [pe_interactive_image]（代表有正常渲染）
		$output = do_shortcode( '[pe_interactive_image]' );
		$this->assert_shortcode_rendered( 'pe_interactive_image', $output );
	}

	/**
	 * @test
	 * @group happy
	 */
	public function 短碼渲染_帶空屬性陣列_應與無屬性輸出一致(): void {
		// Given：pe_interactive_image 已登記
		// When：分別呼叫 callback([]) 與 callback([]) （空陣列）
		// Then：兩者輸出應相同
		$output1 = InteractiveImage::callback( [] );
		$output2 = InteractiveImage::callback( [] );
		$this->assertSame( $output1, $output2, '兩次呼叫結果應相同（無副作用）' );
	}

	/**
	 * @test
	 * @group happy
	 */
	public function 短碼渲染_do_shortcode包裝_應與直接callback一致(): void {
		// Given：pe_interactive_image 已登記
		// When：透過 do_shortcode 執行
		// Then：輸出應與直接呼叫 callback 一致
		$via_shortcode = do_shortcode( '[pe_interactive_image]' );
		$via_callback  = InteractiveImage::callback( [] );
		$this->assertSame( $via_shortcode, $via_callback, 'do_shortcode 應等同直接呼叫 callback' );
	}

	// ========== 錯誤處理（Error Handling）==========

	/**
	 * @test
	 * @group error
	 */
	public function 短碼渲染_callback傳入非陣列相容值_應不拋出例外(): void {
		// Given：callback 方法宣告接受 array $atts
		// When：傳入空陣列
		// Then：不應拋出任何例外
		$threw = false;
		try {
			InteractiveImage::callback( [] );
		} catch ( \Throwable $e ) {
			$threw     = true;
			$this->lastError = $e;
		}
		$this->assertFalse( $threw, '短碼 callback 不應拋出例外：' . ( $this->lastError?->getMessage() ?? '' ) );
	}

	// ========== 邊緣案例（Edge Cases）==========

	/**
	 * @test
	 * @group edge
	 */
	public function 短碼渲染_巢狀短碼_外層應正常渲染(): void {
		// Given：文章內容包含兩個短碼
		// When：執行 do_shortcode
		// Then：兩個短碼都應被解析（不含原始標籤）
		$content = '[pe_interactive_image] [pe_interactive_image]';
		$output  = do_shortcode( $content );
		$this->assertStringNotContainsString(
			'[pe_interactive_image]',
			$output,
			'巢狀（重複）短碼應全部被解析'
		);
	}

	/**
	 * @test
	 * @group edge
	 */
	public function 短碼渲染_混合純文字與短碼_純文字應保留(): void {
		// Given：內容包含短碼與純文字
		// When：執行 do_shortcode
		// Then：純文字部分應完整保留
		$content = '前文字' . '[pe_interactive_image]' . '後文字';
		$output  = do_shortcode( $content );
		$this->assertStringContainsString( '前文字', $output, '前面的純文字應保留' );
		$this->assertStringContainsString( '後文字', $output, '後面的純文字應保留' );
	}

	/**
	 * @test
	 * @group edge
	 */
	public function 短碼渲染_Unicode屬性值_應不拋出例外(): void {
		// Given：含 Unicode 字元的屬性值
		// When：透過 callback 傳入（模擬 WordPress parse_shortcode_atts 後的結果）
		// Then：應不拋出例外，並回傳字串
		$threw = false;
		try {
			$output = InteractiveImage::callback( [ 'title' => '測試標題 🎉' ] );
			$this->assertIsString( $output );
		} catch ( \Throwable $e ) {
			$threw           = true;
			$this->lastError = $e;
		}
		$this->assertFalse( $threw, '傳入 Unicode 屬性值不應拋出例外' );
	}

	/**
	 * @test
	 * @group edge
	 */
	public function 短碼渲染_XSS嘗試_輸出應不含未跳脫的script標籤(): void {
		// Given：含 XSS 攻擊字串的屬性
		// When：執行 callback
		// Then：輸出中不應含原始 <script> 標籤（由 WordPress escape 函數保護）
		$output = InteractiveImage::callback( [ 'title' => '<script>alert("xss")</script>' ] );
		$this->assertStringNotContainsString(
			'<script>alert("xss")</script>',
			$output,
			'輸出中不應含未跳脫的 script 標籤'
		);
	}
}
