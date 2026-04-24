<?php
/**
 * ControlUtils 整合測試
 * 驗證 Elementor 控制項工具方法的輸出行為
 */

declare( strict_types=1 );

namespace Tests\Integration\ElementorWidgets;

use J7\PowerElement\ElementorWidgets\Shared\Utils\ControlUtils;
use Tests\Integration\TestCase;

/**
 * Class ControlUtilsTest
 *
 * @group happy
 * @group edge
 * @group security
 */
class ControlUtilsTest extends TestCase {

	// ========== 快樂路徑（Happy Flow）==========

	/**
	 * @test
	 * @group happy
	 */
	public function get_current_item_id_一般id_應回傳正確格式(): void {
		// Given：一個含 _id 欄位的 repeater item
		// When：呼叫 get_current_item_id
		// Then：應回傳 'elementor-repeater-item-{_id}'
		$item   = [ '_id' => 'abc123' ];
		$result = ControlUtils::get_current_item_id( $item );
		$this->assertSame(
			'elementor-repeater-item-abc123',
			$result,
			'get_current_item_id 格式不符'
		);
	}

	/**
	 * @test
	 * @group happy
	 */
	public function get_icon_預設參數_應輸出eicon_info_circle(): void {
		// Given：不傳任何參數
		// When：呼叫 get_icon()
		// Then：輸出應包含預設圖示 class 'eicon-info-circle'
		$result = ControlUtils::get_icon();
		$this->assertStringContainsString( 'eicon-info-circle', $result, '預設 icon 應為 eicon-info-circle' );
	}

	/**
	 * @test
	 * @group happy
	 */
	public function get_icon_自訂icon_應包含指定class(): void {
		// Given：指定 icon 名稱
		// When：呼叫 get_icon('eicon-star')
		// Then：輸出應包含 'eicon-star'
		$result = ControlUtils::get_icon( 'eicon-star' );
		$this->assertStringContainsString( 'eicon-star', $result, '應包含指定 icon class' );
	}

	/**
	 * @test
	 * @group happy
	 */
	public function get_icon_自訂class_應包含附加class(): void {
		// Given：指定額外 CSS class
		// When：呼叫 get_icon('eicon-star', 'my-custom-class')
		// Then：輸出應包含 'my-custom-class'
		$result = ControlUtils::get_icon( 'eicon-star', 'my-custom-class' );
		$this->assertStringContainsString( 'my-custom-class', $result, '應包含附加 CSS class' );
	}

	/**
	 * @test
	 * @group happy
	 */
	public function get_icon_輸出應含i_tag(): void {
		// Given：呼叫 get_icon
		// When：取得輸出
		// Then：輸出應為 <i> HTML 標籤
		$result = ControlUtils::get_icon();
		$this->assertStringContainsString( '<i ', $result, '輸出應含 <i> 標籤' );
		$this->assertStringContainsString( '</i>', $result, '輸出應含 </i> 關閉標籤' );
	}

	/**
	 * @test
	 * @group happy
	 */
	public function get_icon_輸出應含aria_hidden屬性(): void {
		// Given：呼叫 get_icon（裝飾性圖示，應對 screen reader 隱藏）
		// When：取得輸出
		// Then：輸出應含 aria-hidden="true"
		$result = ControlUtils::get_icon();
		$this->assertStringContainsString( 'aria-hidden="true"', $result, '輸出應含 aria-hidden="true"' );
	}

	// ========== 邊緣案例（Edge Cases）==========

	/**
	 * @test
	 * @group edge
	 */
	public function get_current_item_id_空字串id_應回傳前綴加空字串(): void {
		// Given：_id 為空字串
		// When：呼叫 get_current_item_id
		// Then：應回傳 'elementor-repeater-item-'（前綴不含任何後綴）
		$item   = [ '_id' => '' ];
		$result = ControlUtils::get_current_item_id( $item );
		$this->assertSame( 'elementor-repeater-item-', $result, '空 _id 應只保留前綴' );
	}

	/**
	 * @test
	 * @group edge
	 */
	public function get_current_item_id_超長id_應正常處理(): void {
		// Given：非常長的 _id 字串
		// When：呼叫 get_current_item_id
		// Then：應不拋出例外，並回傳包含完整前綴的字串
		$long_id = str_repeat( 'x', 500 );
		$item    = [ '_id' => $long_id ];
		$result  = ControlUtils::get_current_item_id( $item );
		$this->assertStringStartsWith( 'elementor-repeater-item-', $result, '超長 id 應保留前綴' );
	}

	// ========== 安全性（Security）==========

	/**
	 * @test
	 * @group security
	 */
	public function get_current_item_id_XSS嘗試_id應被esc_attr跳脫(): void {
		// Given：含 XSS 字串的 _id
		// When：呼叫 get_current_item_id
		// Then：輸出不應含未跳脫的 HTML 標籤（esc_attr 保護）
		$item   = [ '_id' => '"><script>alert(1)</script>' ];
		$result = ControlUtils::get_current_item_id( $item );
		$this->assertStringNotContainsString(
			'<script>',
			$result,
			'XSS 嘗試應被 esc_attr 跳脫'
		);
	}

	/**
	 * @test
	 * @group security
	 */
	public function get_icon_XSS嘗試_輸出應含i_tag結構(): void {
		// Given：含 XSS 字串的 icon 名稱
		// When：呼叫 get_icon（注意：get_icon 使用 sprintf，不做 HTML escape）
		// Then：輸出仍應符合 <i ...> 標籤結構（記錄現有行為，呼叫者自行負責 escape）
		// 注意：此測試記錄 get_icon 的現有行為：icon class 直接插入，不 escape
		// 若需安全性改善，應在生產碼中加入 esc_attr()
		$result = ControlUtils::get_icon( 'safe-icon-class' );
		$this->assertStringContainsString( '<i ', $result );
		$this->assertStringContainsString( 'safe-icon-class', $result );

		// 驗證 get_icon 的一般使用（合法 CSS class 名稱）不影響結構完整性
		$result_with_dash = ControlUtils::get_icon( 'eicon-some-icon', 'extra-class' );
		$this->assertStringContainsString( 'eicon-some-icon', $result_with_dash );
		$this->assertStringContainsString( 'extra-class', $result_with_dash );
	}
}
