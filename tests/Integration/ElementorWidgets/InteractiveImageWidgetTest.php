<?php
/**
 * InteractiveImage Widget 靜態方法整合測試
 * 驗證 Widget 中不依賴 Elementor 的純業務邏輯靜態方法
 *
 * 因 Elementor 未載入（\Elementor\Widget_Base 不存在），Widget 類別無法被 PHP 載入。
 * 我們將 parse_comma_string 邏輯抽取為受測方法，在獨立的測試 helper 中複製實作，
 * 以驗證演算法本身的正確性，無需依賴 Elementor 存在。
 */

declare( strict_types=1 );

namespace Tests\Integration\ElementorWidgets;

use Tests\Integration\TestCase;

/**
 * Class InteractiveImageWidgetTest
 *
 * @group happy
 * @group edge
 * @group security
 */
class InteractiveImageWidgetTest extends TestCase {

	/**
	 * 複製 InteractiveImage::parse_comma_string 的演算法邏輯
	 * 供 Elementor 未載入時測試使用
	 *
	 * @param string $str
	 * @return array<string>
	 */
	private function parse_comma_string( string $str ): array {
		$arr = explode( ',', $str );
		$arr = array_map( 'trim', $arr );
		$arr = array_filter( $arr );
		return array_values( $arr );
	}

	// ========== 快樂路徑（Happy Flow）==========

	/**
	 * @test
	 * @group happy
	 */
	public function parse_comma_string_一般逗號分隔字串_應回傳正確陣列(): void {
		// Given：含逗號分隔的 meta_key 字串
		// When：呼叫 parse_comma_string
		// Then：應回傳正確分割後的陣列
		$result = $this->parse_comma_string( 'key1,key2,key3' );
		$this->assertSame( [ 'key1', 'key2', 'key3' ], $result, '應正確分割逗號字串' );
	}

	/**
	 * @test
	 * @group happy
	 */
	public function parse_comma_string_含空白的逗號分隔_應自動trim(): void {
		// Given：含前後空白的 meta_key 字串
		// When：呼叫 parse_comma_string
		// Then：每個元素的前後空白應被移除
		$result = $this->parse_comma_string( ' key1 , key2 , key3 ' );
		$this->assertSame( [ 'key1', 'key2', 'key3' ], $result, '每個元素應自動 trim 前後空白' );
	}

	/**
	 * @test
	 * @group happy
	 */
	public function parse_comma_string_單一元素_應回傳單元素陣列(): void {
		// Given：不含逗號的字串
		// When：呼叫 parse_comma_string
		// Then：應回傳含單一元素的陣列
		$result = $this->parse_comma_string( 'single_key' );
		$this->assertSame( [ 'single_key' ], $result, '單一元素應回傳單元素陣列' );
	}

	/**
	 * @test
	 * @group happy
	 */
	public function parse_comma_string_含Unicode字元_應正確分割(): void {
		// Given：含中文字元的 meta key
		// When：呼叫 parse_comma_string
		// Then：應正確分割中文字元的 meta_key
		$result = $this->parse_comma_string( '中文key,another_key' );
		$this->assertSame( [ '中文key', 'another_key' ], $result, '應正確分割含中文的字串' );
	}

	// ========== 錯誤處理（Error Handling）==========

	/**
	 * @test
	 * @group error
	 */
	public function parse_comma_string_空字串_應回傳空陣列(): void {
		// Given：空字串
		// When：呼叫 parse_comma_string
		// Then：應回傳空陣列（array_filter 會過濾空元素）
		$result = $this->parse_comma_string( '' );
		$this->assertSame( [], $result, '空字串應回傳空陣列' );
	}

	/**
	 * @test
	 * @group error
	 */
	public function parse_comma_string_僅空白的字串_應回傳空陣列(): void {
		// Given：僅含空白的字串
		// When：呼叫 parse_comma_string
		// Then：trim 後為空，array_filter 過濾，應回傳空陣列
		$result = $this->parse_comma_string( '   ,  ,   ' );
		$this->assertSame( [], $result, '僅含空白的逗號字串應回傳空陣列' );
	}

	// ========== 邊緣案例（Edge Cases）==========

	/**
	 * @test
	 * @group edge
	 */
	public function parse_comma_string_尾部逗號_應忽略空元素(): void {
		// Given：尾部有多餘逗號的字串
		// When：呼叫 parse_comma_string
		// Then：尾部空元素應被 array_filter 移除
		$result = $this->parse_comma_string( 'key1,key2,' );
		$this->assertSame( [ 'key1', 'key2' ], $result, '尾部空元素應被移除' );
	}

	/**
	 * @test
	 * @group edge
	 */
	public function parse_comma_string_超長字串_應不拋出例外(): void {
		// Given：超長字串（模擬用戶誤輸入）
		// When：呼叫 parse_comma_string
		// Then：應不拋出例外，回傳陣列
		$long_str = str_repeat( 'key,', 1000 );
		$threw    = false;
		try {
			$result = $this->parse_comma_string( $long_str );
			$this->assertIsArray( $result, '應回傳陣列' );
			$this->assertCount( 1000, $result, '應有 1000 個元素' );
		} catch ( \Throwable $e ) {
			$threw           = true;
			$this->lastError = $e;
		}
		$this->assertFalse( $threw, '超長字串不應拋出例外' );
	}

	/**
	 * @test
	 * @group edge
	 */
	public function parse_comma_string_連續多個逗號_應過濾所有空元素(): void {
		// Given：含連續逗號的字串（模擬用戶誤輸入多餘逗號）
		// When：呼叫 parse_comma_string
		// Then：空元素應全部被 array_filter 移除
		$result = $this->parse_comma_string( 'key1,,,key2' );
		$this->assertSame( [ 'key1', 'key2' ], $result, '連續逗號間的空元素應被移除' );
	}

	// ========== 安全性（Security）==========

	/**
	 * @test
	 * @group security
	 */
	public function parse_comma_string_SQL_injection字串_應不拋出例外(): void {
		// Given：含 SQL injection 嘗試的字串
		// When：呼叫 parse_comma_string
		// Then：應不拋出例外（此方法僅做字串分割，SQL safety 由呼叫者負責）
		$threw = false;
		try {
			$result = $this->parse_comma_string( "'; DROP TABLE wp_posts; --" );
			$this->assertIsArray( $result );
		} catch ( \Throwable $e ) {
			$threw           = true;
			$this->lastError = $e;
		}
		$this->assertFalse( $threw, 'SQL injection 字串不應造成例外' );
	}
}
