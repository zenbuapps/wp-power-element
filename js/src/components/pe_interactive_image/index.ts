import './index.scss'
import $ from 'jquery'

const SELECTOR = '.pe_interactive_image'

$(document).ready(function () {
	const isUnique = $(SELECTOR).data('is-unique') === 'yes'

	// 互動效果，滑鼠 hover list item 時顯示卡片
	$(SELECTOR).on('mouseenter', 'div[data-list-item-id]', function () {
		const id = $(this).data('list-item-id')
		if (isUnique) {
			$(
				`div.pe_interactive_image__card:not([data-card-post-id=${id}])`,
			).fadeOut()
			$(`div.pe_interactive_image__card[data-card-post-id=${id}]`).fadeIn()
		} else {
			$(`div.pe_interactive_image__card:not([data-card-id=${id}])`).fadeOut()
			$(`div.pe_interactive_image__card[data-card-id=${id}]`).fadeIn()
		}
	})

	// 滑鼠移出 card div 時隱藏卡片
	$(SELECTOR).on('mouseleave', 'div[data-card-id]', function () {
		$(this).fadeOut()
	})




	// 點擊 icon 時，顯示卡片
	$(SELECTOR).on('click', '.pe_interactive_image__icon', function () {
		const card = $(this).find('div.pe_interactive_image__card')
		const id = card.data('card-id')
		$(`div.pe_interactive_image__card:not([data-card-id=${id}])`).fadeOut()
		card.fadeIn()
	})

	setListHeight()

	// $(window).resize(function(){
	// debounce
	// 	setListHeight();
	// })

	// 將列表高度與圖片等高，超過就顯示 scroll
	function setListHeight() {
		const imgH = $('img.pe_interactive_image__bg_image').height()
		$('div.pe_interactive_image__right').height(imgH)
	}
})
