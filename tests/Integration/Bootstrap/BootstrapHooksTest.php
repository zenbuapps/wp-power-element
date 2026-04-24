<?php
/**
 * Bootstrap Hook 登記整合測試
 * 驗證 Bootstrap 是否正確登記所有必要的 WordPress hooks
 */

declare( strict_types=1 );

namespace Tests\Integration\Bootstrap;

use J7\PowerElement\Bootstrap;
use Tests\Integration\TestCase;

/**
 * Class BootstrapHooksTest
 *
 * @group smoke
 * @group happy
 */
class BootstrapHooksTest extends TestCase {

	// ========== 冒煙測試（Smoke）==========

	/**
	 * @test
	 * @group smoke
	 */
	public function Bootstrap_應成功初始化(): void {
		// Given：WordPress 已載入
		// When：取得 Bootstrap 實例
		// Then：實例應存在
		$instance = Bootstrap::instance();
		$this->assertInstanceOf(
			Bootstrap::class,
			$instance,
			'Bootstrap::instance() 應回傳 Bootstrap 實例'
		);
	}

	/**
	 * @test
	 * @group smoke
	 */
	public function Bootstrap_wp_enqueue_scripts_hook_應已登記(): void {
		// Given：Bootstrap 已初始化
		// When：檢查 wp_enqueue_scripts hook
		// Then：frontend_register_script 應已登記於 priority 200
		$has_action = has_action( 'wp_enqueue_scripts', [ Bootstrap::class, 'frontend_register_script' ] );
		$this->assertNotFalse(
			$has_action,
			'wp_enqueue_scripts 的 frontend_register_script callback 未登記'
		);
		$this->assertSame(
			200,
			$has_action,
			'wp_enqueue_scripts 應在 priority 200 登記'
		);
	}

	// ========== 快樂路徑（Happy Flow）==========

	/**
	 * @test
	 * @group happy
	 */
	public function Bootstrap_get_handle_ButtonA_應回傳正確handle(): void {
		// Given：Bootstrap 已初始化
		// When：呼叫 get_handle('ButtonA')
		// Then：應回傳 'power-element-ButtonA'
		$handle = Bootstrap::get_handle( 'ButtonA' );
		$this->assertStringContainsString( 'ButtonA', $handle, 'handle 應包含元件名稱' );
		$this->assertStringContainsString( 'power-element', $handle, 'handle 應包含外掛前綴' );
	}

	/**
	 * @test
	 * @group happy
	 */
	public function Bootstrap_get_handle_ButtonB_應回傳正確handle(): void {
		// Given：Bootstrap 已初始化
		// When：呼叫 get_handle('ButtonB')
		// Then：應回傳包含 'ButtonB' 的 handle
		$handle = Bootstrap::get_handle( 'ButtonB' );
		$this->assertStringContainsString( 'ButtonB', $handle, 'handle 應包含元件名稱' );
	}

	/**
	 * @test
	 * @group happy
	 */
	public function Bootstrap_get_handle_pe_interactive_image_應回傳正確handle(): void {
		// Given：Bootstrap 已初始化
		// When：呼叫 get_handle('pe_interactive_image')
		// Then：應回傳包含 'pe_interactive_image' 的 handle
		$handle = Bootstrap::get_handle( 'pe_interactive_image' );
		$this->assertStringContainsString( 'pe_interactive_image', $handle, 'handle 應包含元件名稱' );
	}

	/**
	 * @test
	 * @group happy
	 */
	public function Bootstrap_is_component_已知元件_應回傳true(): void {
		// Given：Bootstrap 定義的已知元件列表
		// When：呼叫 is_component 傳入已知元件名稱
		// Then：應回傳 true
		$this->assertTrue( Bootstrap::is_component( 'ButtonA' ), 'ButtonA 應是元件' );
		$this->assertTrue( Bootstrap::is_component( 'ButtonB' ), 'ButtonB 應是元件' );
		$this->assertTrue( Bootstrap::is_component( 'pe_interactive_image' ), 'pe_interactive_image 應是元件' );
	}

	/**
	 * @test
	 * @group happy
	 */
	public function Bootstrap_is_component_不存在的元件_應回傳false(): void {
		// Given：不存在的元件名稱
		// When：呼叫 is_component
		// Then：應回傳 false
		$this->assertFalse( Bootstrap::is_component( 'NotExists' ), '不存在的元件應回傳 false' );
		$this->assertFalse( Bootstrap::is_component( '' ), '空字串應回傳 false' );
	}

	// ========== 錯誤處理（Error Handling）==========

	/**
	 * @test
	 * @group error
	 */
	public function Bootstrap_enqueue_不存在的元件_應拋出Exception(): void {
		// Given：Bootstrap 實例
		// When：呼叫 enqueue 傳入不存在的元件名稱
		// Then：應拋出 Exception
		$instance = Bootstrap::instance();
		$threw    = false;
		try {
			$instance->enqueue( 'NotAComponent' );
		} catch ( \Exception $e ) {
			$threw           = true;
			$this->lastError = $e;
		}
		$this->assertTrue( $threw, '傳入不存在元件的 enqueue 應拋出例外' );
		$this->assertStringContainsString(
			'NotAComponent',
			$this->lastError?->getMessage() ?? '',
			'例外訊息應包含元件名稱'
		);
	}

	// ========== 邊緣案例（Edge Cases）==========

	/**
	 * @test
	 * @group edge
	 */
	public function Bootstrap_get_handle_超長名稱_應不拋出例外(): void {
		// Given：超長元件名稱字串（256 字元）
		// When：呼叫 get_handle
		// Then：應回傳字串，不拋出例外
		$long_name = str_repeat( 'a', 256 );
		$handle    = Bootstrap::get_handle( $long_name );
		$this->assertIsString( $handle, 'get_handle 應始終回傳字串' );
	}

	/**
	 * @test
	 * @group edge
	 */
	public function Bootstrap_get_handle_含特殊字元_應回傳字串(): void {
		// Given：含特殊字元的名稱（如 SQL injection 嘗試）
		// When：呼叫 get_handle
		// Then：應回傳字串，不拋出例外
		$special = "name'; DROP TABLE wp_posts; --";
		$handle  = Bootstrap::get_handle( $special );
		$this->assertIsString( $handle, '特殊字元名稱不應造成例外' );
	}

	/**
	 * @test
	 * @group edge
	 */
	public function Bootstrap_is_component_null字串_應回傳false(): void {
		// Given：數字 0 轉為字串的情況
		// When：呼叫 is_component('0')
		// Then：應回傳 false（不存在的元件）
		$this->assertFalse( Bootstrap::is_component( '0' ), '\'0\' 不應是元件' );
	}
}
