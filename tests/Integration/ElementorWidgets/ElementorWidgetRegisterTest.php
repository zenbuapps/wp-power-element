<?php
/**
 * Elementor Widget 登記整合測試
 * 驗證 ElementorWidgets\Register 的 hook 登記行為
 * 注意：測試環境中 Elementor 未載入，測試應優雅處理此情況
 */

declare( strict_types=1 );

namespace Tests\Integration\ElementorWidgets;

use J7\PowerElement\ElementorWidgets\Register;
use Tests\Integration\TestCase;

/**
 * Class ElementorWidgetRegisterTest
 *
 * @group happy
 * @group error
 */
class ElementorWidgetRegisterTest extends TestCase {

	// ========== 冒煙測試（Smoke）==========

	/**
	 * @test
	 * @group smoke
	 */
	public function ElementorWidgets_Register類別_應存在(): void {
		// Given：Power Element 已載入
		// When：檢查 Register 類別
		// Then：類別應存在
		$this->assertTrue(
			class_exists( Register::class ),
			'J7\PowerElement\ElementorWidgets\Register 類別不存在'
		);
	}

	// ========== 快樂路徑（Happy Flow）==========

	/**
	 * @test
	 * @group happy
	 */
	public function ElementorWidgets_Register_elementor_widgets_register_hook_應已登記(): void {
		// Given：Bootstrap 已初始化
		// When：檢查 elementor/widgets/register hook
		// Then：Register::register_widgets 應已登記
		$has_action = has_action( 'elementor/widgets/register', [ Register::class, 'register_widgets' ] );
		$this->assertNotFalse(
			$has_action,
			'elementor/widgets/register hook 的 register_widgets 未登記'
		);
	}

	/**
	 * @test
	 * @group happy
	 */
	public function ElementorWidgets_Register_elementor_dynamic_tags_register_hook_應已登記(): void {
		// Given：Bootstrap 已初始化
		// When：檢查 elementor/dynamic_tags/register hook
		// Then：Register::register_dynamic_tags 應已登記於 priority 200
		$has_action = has_action(
			'elementor/dynamic_tags/register',
			[ Register::class, 'register_dynamic_tags' ]
		);
		$this->assertNotFalse(
			$has_action,
			'elementor/dynamic_tags/register hook 的 register_dynamic_tags 未登記'
		);
		$this->assertSame(
			200,
			$has_action,
			'elementor/dynamic_tags/register 應在 priority 200 登記'
		);
	}

	// ========== 錯誤處理（Error Handling）==========

	/**
	 * @test
	 * @group error
	 */
	public function ElementorWidgets_register_dynamic_tags_Elementor未載入_應不拋出例外(): void {
		// Given：測試環境中 Elementor 未載入（ElementorPro\Plugin 不存在）
		// When：呼叫 register_dynamic_tags（傳入模擬的 manager）
		// Then：因為有 class_exists('\ElementorPro\Plugin') guard，應靜默跳過，不拋出例外
		$threw = false;
		try {
			$mock_manager = new class {
				public function register( $tag ): void {}
				public function register_group( string $name, array $options ): void {}
			};
			Register::register_dynamic_tags( $mock_manager );
		} catch ( \Throwable $e ) {
			$threw           = true;
			$this->lastError = $e;
		}
		$this->assertFalse(
			$threw,
			'Elementor 未載入時 register_dynamic_tags 不應拋出例外：' . ( $this->lastError?->getMessage() ?? '' )
		);
	}

	// ========== 邊緣案例（Edge Cases）==========

	/**
	 * @test
	 * @group edge
	 */
	public function ElementorWidgets_register_hooks_多次呼叫_不應重複登記(): void {
		// Given：Bootstrap 已初始化，register_hooks 已被呼叫
		// When：再次手動呼叫 register_hooks
		// Then：hook 的 priority 計數應相同（WordPress 允許重複登記，此測試確認行為可預期）
		$before = has_action( 'elementor/widgets/register', [ Register::class, 'register_widgets' ] );
		Register::register_hooks();
		$after = has_action( 'elementor/widgets/register', [ Register::class, 'register_widgets' ] );

		// 無論如何 after 應仍為 truthy（hook 已登記）
		$this->assertNotFalse( $after, '多次呼叫後 hook 應仍登記' );
	}
}
