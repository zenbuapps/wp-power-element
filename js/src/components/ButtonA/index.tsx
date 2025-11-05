import { createRoot, StrictMode } from '@wordpress/element'
import { App } from './App'


const SELECTOR = '.button-a'
const containers = document.querySelectorAll(SELECTOR)

function init() {
	if (!containers.length) return

	containers.forEach((container) => {
		createRoot(container).render(
			<StrictMode>
				<App />
			</StrictMode>,
		)
	})
}

init()
