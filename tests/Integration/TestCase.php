<?php
/**
 * 整合測試基礎類別
 * 所有 Power Element 整合測試必須繼承此類別
 */

declare( strict_types=1 );

namespace Tests\Integration;

/**
 * Class TestCase
 * 整合測試基礎類別，提供共用 helper methods
 */
abstract class TestCase extends \WP_UnitTestCase {

	/**
	 * 最後發生的錯誤（用於驗證操作是否失敗）
	 *
	 * @var \Throwable|null
	 */
	protected ?\Throwable $lastError = null;

	/**
	 * 查詢結果（用於驗證 Query 操作的回傳值）
	 *
	 * @var mixed
	 */
	protected mixed $queryResult = null;

	/**
	 * ID 映射表（名稱 → ID 等）
	 *
	 * @var array<string, int>
	 */
	protected array $ids = [];

	/**
	 * 設定（每個測試前執行）
	 */
	public function set_up(): void {
		parent::set_up();

		$this->lastError   = null;
		$this->queryResult = null;
		$this->ids         = [];
	}

	/**
	 * 清理（每個測試後執行）
	 * WP_UnitTestCase 會自動回滾資料庫事務
	 */
	public function tear_down(): void {
		parent::tear_down();
	}

	// ========== 資料建立 Helper ==========

	/**
	 * 建立測試文章並設定縮圖
	 *
	 * @param array<string, mixed> $args 覆蓋預設值
	 * @return int 文章 ID
	 */
	protected function create_post( array $args = [] ): int {
		$defaults = [
			'post_title'   => '測試文章',
			'post_content' => '這是測試文章內容',
			'post_excerpt' => '這是測試文章摘要',
			'post_status'  => 'publish',
			'post_type'    => 'post',
		];

		$post_args = wp_parse_args( $args, $defaults );
		return $this->factory()->post->create( $post_args );
	}

	/**
	 * 建立測試文章並設定指定 meta
	 *
	 * @param array<string, mixed>  $post_args 文章設定
	 * @param array<string, mixed>  $meta_args meta key => value 對照表
	 * @return int 文章 ID
	 */
	protected function create_post_with_meta( array $post_args = [], array $meta_args = [] ): int {
		$post_id = $this->create_post( $post_args );
		foreach ( $meta_args as $key => $value ) {
			update_post_meta( $post_id, $key, $value );
		}
		return $post_id;
	}

	// ========== 捷徑碼 Helper ==========

	/**
	 * 執行短碼並取得輸出
	 *
	 * @param string               $shortcode_tag 短碼名稱（不含方括號）
	 * @param array<string, mixed> $atts 短碼屬性
	 * @return string 輸出字串
	 */
	protected function do_shortcode( string $shortcode_tag, array $atts = [] ): string {
		$atts_str = '';
		foreach ( $atts as $key => $value ) {
			$atts_str .= " {$key}=\"{$value}\"";
		}
		return do_shortcode( "[{$shortcode_tag}{$atts_str}]" );
	}

	// ========== 斷言 Helper ==========

	/**
	 * 斷言操作成功（$this->lastError 應為 null）
	 */
	protected function assert_operation_succeeded(): void {
		$this->assertNull(
			$this->lastError,
			sprintf( '預期操作成功，但發生錯誤：%s', $this->lastError?->getMessage() )
		);
	}

	/**
	 * 斷言操作失敗（$this->lastError 不應為 null）
	 */
	protected function assert_operation_failed(): void {
		$this->assertNotNull( $this->lastError, '預期操作失敗，但沒有發生錯誤' );
	}

	/**
	 * 斷言操作失敗且錯誤訊息包含指定文字
	 *
	 * @param string $msg 期望錯誤訊息包含的文字
	 */
	protected function assert_operation_failed_with_message( string $msg ): void {
		$this->assertNotNull( $this->lastError, '預期操作失敗' );
		$this->assertStringContainsString(
			$msg,
			$this->lastError->getMessage(),
			"錯誤訊息不包含 \"{$msg}\"，實際訊息：{$this->lastError->getMessage()}"
		);
	}

	/**
	 * 斷言 action hook 被觸發
	 *
	 * @param string $action_name action 名稱
	 */
	protected function assert_action_fired( string $action_name ): void {
		$this->assertGreaterThan(
			0,
			did_action( $action_name ),
			"Action '{$action_name}' 未被觸發"
		);
	}

	/**
	 * 斷言短碼已被 WordPress 核心登記
	 *
	 * @param string $tag 短碼名稱
	 */
	protected function assert_shortcode_registered( string $tag ): void {
		$this->assertTrue(
			shortcode_exists( $tag ),
			"短碼 [{$tag}] 未被登記"
		);
	}

	/**
	 * 斷言短碼輸出包含指定字串
	 *
	 * @param string $expected 期望包含的字串
	 * @param string $output   短碼輸出
	 * @param string $message  失敗時的錯誤訊息
	 */
	protected function assert_shortcode_output_contains( string $expected, string $output, string $message = '' ): void {
		$default_message = $message ?: "短碼輸出中找不到 \"{$expected}\"";
		$this->assertStringContainsString( $expected, $output, $default_message );
	}

	/**
	 * 斷言短碼輸出不包含原始短碼標籤（代表有正常渲染）
	 *
	 * @param string $tag    短碼名稱
	 * @param string $output 短碼輸出
	 */
	protected function assert_shortcode_rendered( string $tag, string $output ): void {
		$this->assertStringNotContainsString(
			"[{$tag}",
			$output,
			"短碼 [{$tag}] 未被渲染，輸出中仍含原始短碼標籤"
		);
	}
}
